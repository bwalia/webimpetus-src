<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
<div class="page_title_right" style="float:right;">Total Records: <span id="total"><?=$total?></span> </div>
<form id="example_filter" class="dataTables_filter align-items-right"><label>Filter by keyword:<input type="text" class="form-control" placeholder="" name="filter" aria-controls="example" value="<?=@$_GET['filter']?>"></label></form>
    <div class="QA_table table-responsive ">



        <!-- table-responsive -->
        <table id='myTable' class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Message</th>
                    <th scope="col">Created at</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($enquiries as $row) : ?>
                    <tr data-link=<?= "/".$tableName."/edit/".$row['id'];?>>
                        <td class="f_s_12 f_w_200"><?= $row['id']; ?></td>
                        <td class="f_s_12 f_w_200"><?= substr($row['name'],0,20).(strlen($row['name'])>20?'...':''); ?></td>
                        <td class="f_s_12 f_w_400"><?= substr($row['email'],0,20).(strlen($row['email'])>20?'...':''); ?></td>
                        <td class="f_s_12 f_w_600  ">
                            <p class="pd10"> <?=substr($row['message'],0,20).(strlen($row['message'])>20?'...':''); ?></p>
                        </td>
                        <td class="f_s_12 f_w_200  ">
                            <p class="pd10"> <?= $row['created']; ?></p>
                        </td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/enquiries/delete/<?= $row['id']; ?>"> <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="/enquiries/edit/<?= $row['id']; ?>"> <i class="fas fa-edit"></i> Edit</a </div>
                                    </div>
                                </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
       
        <nav aria-label="Page navigation">
	        <ul class="pagination"  id="myPager"></ul>        
        </nav>

    </div>
</div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<?php require_once(APPPATH . 'Views/common/pagination.php'); ?>
<script>

    var executed = false;
    window.createPagination = function(pageNum) {
            pageNum=pageNum+1
            $.ajax({
                url: '<?=base_url()?>/index.php/enquiries/loadData/?page='+pageNum+'<?=!empty($_GET['filter'])?'&filter='.$_GET['filter']:''?>',
                type: 'get',
                dataType: 'json',
                success: function(responseData){
                    //$('#pagination').html(responseData.pagination);
                    if (!executed) {
                        $('#myTable').pageMe({
                            pagerSelector: '#myPager',
                            showPrevNext: true,
                            hidePageNumbers: false,
                            perPage: 10,
                            total: parseInt(<?=$total?>)
                        });
                        executed = true;

                    }
                    paginationData(responseData.results);
                }
            });
        }

        window.paginationData = function(data) {
            $('#myTable tbody').empty();
            for(emp in data){
                var empRow = "<tr data-link='/<?=$tableName."/edit/"?>"+data[emp].id+"'>";
                empRow += "<td>"+ data[emp].id +"</td>";
                empRow += "<td>"+ data[emp].name.substr(0, 20)+(data[emp].name.length>20?'...':'') +"</td>";
                empRow += "<td>"+ data[emp].email.substr(0, 20)+(data[emp].email.length>20?'...':'') +"</td>"
                empRow += "<td>"+ data[emp].message.substr(0, 20)+(data[emp].message.length>20?'...':'')+"</td>"
                empRow += "<td>"+ data[emp].created +"</td>"
                empRow += '<td class="f_s_12 f_w_400 text-right"><div class="header_more_tool"> <div class="dropdown"> <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">  <i class="ti-more-alt"></i></span> <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"><a class="dropdown-item" onclick="return confirm(\'Are you sure want to delete?\');" href="/enquiries/delete/'+data[emp].id+'"> <i class="ti-trash"></i> Delete</a><a class="dropdown-item" href="/enquiries/edit/'+data[emp].id+'"> <i class="fas fa-edit"></i> Edit</a </div> </div></div></td>'
                //empRow += "<td>"+ data[emp].designation +"</td>"
                //empRow += "<td>"+ data[emp].address +"</td>";
                empRow += "</tr>";
                console.log($('#myTable tbody').length)
                $('#myTable tbody').append(empRow);					
            }
        }
$(document).ready(function () {    
    window.createPagination(0);
});

</script>