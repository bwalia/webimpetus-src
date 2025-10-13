<?php

namespace App\Controllers;

use App\Controllers\Core\CommonController;
use App\Libraries\UUID;

class Interviews extends CommonController
{
    protected $interviewModel;
    protected $candidateModel;
    protected $jobModel;
    protected $applicationModel;

    public function __construct()
    {
        parent::__construct();
        $this->interviewModel = new \App\Models\Core\Common_model();
        $this->candidateModel = new \App\Models\Core\Common_model();
        $this->jobModel = new \App\Models\Core\Common_model();
        $this->applicationModel = new \App\Models\Core\Common_model();
    }

    /**
     * Dashboard - List all interviews
     */
    public function index()
    {
        $this->data['title'] = 'Interview Management';
        $this->data['tableName'] = 'interviews';
        $this->data['menuName'] = 'Interviews';
        $this->data['page_title'] = 'Interviews';

        // Get statistics
        $builder = $this->db->table('interviews');
        $builder->where('uuid_business_id', $this->businessUuid);

        $this->data['total_interviews'] = $builder->countAllResults(false);
        $this->data['upcoming_interviews'] = $builder->where('scheduled_date >=', date('Y-m-d'))->where('status', 'scheduled')->countAllResults(false);

        $builder2 = $this->db->table('interviews');
        $builder2->where('uuid_business_id', $this->businessUuid);
        $this->data['completed_interviews'] = $builder2->where('status', 'completed')->countAllResults();

        echo view('interviews/dashboard', $this->data);
    }

    /**
     * Get interviews data for dashboard grid
     */
    public function getInterviews()
    {
        $builder = $this->db->table('interviews as i');
        $builder->select('i.*, j.title as job_title, j.reference_number');
        $builder->join('jobs j', 'i.job_id = j.id', 'left');
        $builder->where('i.uuid_business_id', $this->businessUuid);
        $builder->orderBy('i.scheduled_date', 'DESC');
        $builder->orderBy('i.scheduled_time', 'DESC');

        $interviews = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => $interviews
        ]);
    }

    /**
     * Schedule new interview form
     */
    public function schedule()
    {
        $data['title'] = 'Schedule Interview';
        $data['tableName'] = 'interviews';
        $data['menuName'] = 'Schedule Interview';

        // Get all open jobs
        $jobBuilder = $this->db->table('jobs');
        $jobBuilder->where('uuid_business_id', $this->businessUuid);
        $jobBuilder->whereIn('status', ['open', 'draft']);
        $jobBuilder->orderBy('title', 'ASC');
        $data['jobs'] = $jobBuilder->get()->getResultArray();

        // Get users for interviewer selection
        $userBuilder = $this->db->table('users');
        $userBuilder->select('id, name, email');
        $userBuilder->where('uuid_business_id', $this->businessUuid);
        $userBuilder->orderBy('name', 'ASC');
        $data['users'] = $userBuilder->get()->getResultArray();

        $data['interview'] = null;
        if ($uuid = $this->request->getGet('uuid')) {
            $builder = $this->db->table('interviews');
            $builder->where('uuid', $uuid);
            $builder->where('uuid_business_id', $this->businessUuid);
            $data['interview'] = $builder->get()->getRow();
        }

        return view('interviews/schedule', $data);
    }

    /**
     * Save interview
     */
    public function save()
    {
        $uuid = $this->request->getPost('uuid');
        $isNew = empty($uuid);

        if ($isNew) {
            $uuid = UUID::v5(UUID::v4(), 'interviews');
        }

        $jobId = $this->request->getPost('job_id');

        $data = [
            'uuid' => $uuid,
            'uuid_business_id' => $this->businessUuid,
            'job_id' => !empty($jobId) ? $jobId : null,
            'interview_title' => $this->request->getPost('interview_title'),
            'interview_type' => $this->request->getPost('interview_type'),
            'interview_round' => $this->request->getPost('interview_round') ?: 1,
            'scheduled_date' => $this->request->getPost('scheduled_date'),
            'scheduled_time' => $this->request->getPost('scheduled_time'),
            'duration_minutes' => $this->request->getPost('duration_minutes') ?: 60,
            'timezone' => $this->request->getPost('timezone') ?: 'Europe/London',
            'platform' => $this->request->getPost('platform'),
            'meeting_link' => $this->request->getPost('meeting_link'),
            'meeting_id' => $this->request->getPost('meeting_id'),
            'meeting_password' => $this->request->getPost('meeting_password'),
            'location_address' => $this->request->getPost('location_address'),
            'location_room' => $this->request->getPost('location_room'),
            'interviewer_ids' => json_encode($this->request->getPost('interviewer_ids') ?: []),
            'instructions' => $this->request->getPost('instructions'),
            'internal_notes' => $this->request->getPost('internal_notes'),
            'agenda' => $this->request->getPost('agenda'),
            'status' => $this->request->getPost('status') ?: 'scheduled',
            'send_reminders' => $this->request->getPost('send_reminders') ? 1 : 0,
            'reminder_hours_before' => $this->request->getPost('reminder_hours_before') ?: 24,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($isNew) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = session('uuid');
        }

        $builder = $this->db->table('interviews');

        if ($isNew) {
            $builder->insert($data);
        } else {
            $builder->where('uuid', $uuid);
            $builder->where('uuid_business_id', $this->businessUuid);
            $builder->update($data);
        }

        session()->setFlashdata('message', 'Interview saved successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/interviews/view/' . $uuid);
    }

    /**
     * View interview details with candidates
     */
    public function view($uuid)
    {
        $builder = $this->db->table('interviews as i');
        $builder->select('i.*, j.title as job_title, j.reference_number');
        $builder->join('jobs j', 'i.job_id = j.id', 'left');
        $builder->where('i.uuid', $uuid);
        $builder->where('i.uuid_business_id', $this->businessUuid);

        $interview = $builder->get()->getRowArray();

        if (!$interview) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Interview not found');
        }

        // Get candidates for this interview
        $candidateBuilder = $this->db->table('interview_candidates as ic');
        $candidateBuilder->select('ic.*, ja.cv_file_path, ja.id as application_id, ja.applicant_name, ja.applicant_email');
        $candidateBuilder->join('job_applications ja', 'ic.job_application_id = ja.id', 'left');
        $candidateBuilder->where('ic.interview_id', $interview['id']);
        $candidateBuilder->orderBy('ic.candidate_name', 'ASC');

        $candidates = $candidateBuilder->get()->getResultArray();

        // Get available candidates from job applications if job is linked
        $availableCandidates = [];
        if ($interview['job_id']) {
            $appBuilder = $this->db->table('job_applications as ja');
            $appBuilder->select('ja.id, ja.applicant_name, ja.applicant_email, ja.applicant_phone, ja.status');
            $appBuilder->where('ja.job_id', $interview['job_id']);
            $appBuilder->whereIn('ja.status', ['shortlisted', 'new', 'reviewing']);

            // Exclude already added candidates
            if (!empty($candidates)) {
                $existingAppIds = array_column($candidates, 'job_application_id');
                $appBuilder->whereNotIn('ja.id', $existingAppIds);
            }

            $availableCandidates = $appBuilder->get()->getResultArray();
        }

        $data = [
            'interview' => $interview,
            'candidates' => $candidates,
            'availableCandidates' => $availableCandidates,
            'title' => 'Interview: ' . $interview['interview_title'],
            'tableName' => 'interviews',
            'menuName' => 'Interview Details'
        ];

        return view('interviews/view', $data);
    }

    /**
     * Add candidates to interview
     */
    public function addCandidates()
    {
        $interviewUuid = $this->request->getPost('interview_uuid');
        $candidateIds = $this->request->getPost('candidate_ids') ?: [];

        // Get interview
        $builder = $this->db->table('interviews');
        $builder->where('uuid', $interviewUuid);
        $builder->where('uuid_business_id', $this->businessUuid);
        $interview = $builder->get()->getRow();

        if (!$interview) {
            return $this->response->setJSON(['status' => false, 'message' => 'Interview not found']);
        }

        $added = 0;
        foreach ($candidateIds as $appId) {
            // Get application details
            $appBuilder = $this->db->table('job_applications');
            $appBuilder->where('id', $appId);
            $application = $appBuilder->get()->getRow();

            if ($application) {
                // Check if already added
                $existingBuilder = $this->db->table('interview_candidates');
                $existingBuilder->where('interview_id', $interview->id);
                $existingBuilder->where('job_application_id', $appId);

                if ($existingBuilder->countAllResults() == 0) {
                    $candidateData = [
                        'uuid' => UUID::v5(UUID::v4(), 'interview_candidates'),
                        'uuid_business_id' => $this->businessUuid,
                        'interview_id' => $interview->id,
                        'job_application_id' => $application->id,
                        'contact_id' => $application->contact_id,
                        'candidate_name' => $application->applicant_name,
                        'candidate_email' => $application->applicant_email,
                        'candidate_phone' => $application->applicant_phone,
                        'attendance_status' => 'invited',
                        'evaluation_status' => 'pending',
                        'decision' => 'pending',
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    $this->db->table('interview_candidates')->insert($candidateData);
                    $added++;
                }
            }
        }

        // Update candidate count
        $countBuilder = $this->db->table('interview_candidates');
        $countBuilder->where('interview_id', $interview->id);
        $totalCandidates = $countBuilder->countAllResults();

        $this->db->table('interviews')
            ->where('id', $interview->id)
            ->update(['total_candidates' => $totalCandidates]);

        return $this->response->setJSON([
            'status' => true,
            'message' => "{$added} candidate(s) added successfully"
        ]);
    }

    /**
     * Update candidate evaluation
     */
    public function updateEvaluation()
    {
        $candidateId = $this->request->getPost('candidate_id');
        $evaluationStatus = $this->request->getPost('evaluation_status');
        $rating = $this->request->getPost('rating') ?: 0;
        $selectionTags = $this->request->getPost('selection_tags');
        $feedback = $this->request->getPost('feedback');
        $decision = $this->request->getPost('decision');
        $nextSteps = $this->request->getPost('next_steps');

        $updateData = [
            'evaluation_status' => $evaluationStatus,
            'rating' => $rating,
            'selection_tags' => $selectionTags,
            'feedback' => $feedback,
            'decision' => $decision,
            'next_steps' => $nextSteps,
            'evaluated_at' => date('Y-m-d H:i:s'),
            'evaluated_by' => session('uuid'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $builder = $this->db->table('interview_candidates');
        $builder->where('id', $candidateId);
        $builder->where('uuid_business_id', $this->businessUuid);
        $result = $builder->update($updateData);

        return $this->response->setJSON([
            'status' => $result ? true : false,
            'message' => $result ? 'Evaluation updated successfully' : 'Error updating evaluation'
        ]);
    }

    /**
     * Send reminders to candidates
     */
    public function sendReminders($interviewUuid)
    {
        // Get interview details
        $builder = $this->db->table('interviews');
        $builder->where('uuid', $interviewUuid);
        $builder->where('uuid_business_id', $this->businessUuid);
        $interview = $builder->get()->getRow();

        if (!$interview) {
            return $this->response->setJSON(['status' => false, 'message' => 'Interview not found']);
        }

        // Get candidates
        $candidateBuilder = $this->db->table('interview_candidates');
        $candidateBuilder->where('interview_id', $interview->id);
        $candidateBuilder->whereIn('attendance_status', ['invited', 'confirmed']);
        $candidates = $candidateBuilder->get()->getResultArray();

        $sent = 0;
        foreach ($candidates as $candidate) {
            // Send email (implement actual email sending)
            // Send WhatsApp (implement actual WhatsApp sending)

            // Update candidate
            $this->db->table('interview_candidates')
                ->where('id', $candidate['id'])
                ->update([
                    'reminder_sent' => 1,
                    'reminder_sent_at' => date('Y-m-d H:i:s'),
                ]);

            $sent++;
        }

        // Update interview
        $this->db->table('interviews')
            ->where('id', $interview->id)
            ->update([
                'reminder_sent' => 1,
                'reminder_sent_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => "Reminders sent to {$sent} candidate(s)"
        ]);
    }

    /**
     * Delete interview
     */
    public function delete($uuid)
    {
        $builder = $this->db->table('interviews');
        $builder->where('uuid', $uuid);
        $builder->where('uuid_business_id', $this->businessUuid);
        $builder->delete();

        session()->setFlashdata('message', 'Interview deleted successfully!');
        session()->setFlashdata('alert-class', 'alert-success');

        return redirect()->to('/interviews');
    }
}
