$webimpetus_info_array = $this->loadWebImpetusInfo();
		
		if (array_key_exists("version",$webimpetus_info_array))
		{
			setenv("APP_FULL_VERSION_NO", $webimpetus_info_array["version"]);
		}
	 	 else
		{
		echo "Version does not exist!";
		}

		if (array_key_exists("build",$webimpetus_info_array))
		{
			setenv("APP_FULL_BUILD_NO", $webimpetus_info_array["build"]);
		}
	 	 else
		{
		echo "Build no does not exist!";
		}


        public function loadWebImpetusInfo()
	{
		$json_decode = [];
		$webimpetusJSPNFilePath = APPPATH . "webimpetus.json";
		if (file_exists($webimpetusJSPNFilePath)) {
			$json = file_get_contents ($webimpetusJSPNFilePath);
		// Decode the JSON file
		$json_data = json_decode($json,true);
		}
		return json_decode;
	}	

	  
