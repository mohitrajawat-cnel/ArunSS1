<?php
/*
  Plugin Name: Quiz App
  Plugin URI: https://google.com/
  Description:Coins api
  Version: 1.0.0
  Author: Priya & Revanda & Faiz
 */

add_action('rest_api_init', 'registerQuickRouteshwe');

function registerQuickRouteshwe() {

    //route = quick booking & gospa

    register_rest_route('coinapi/v1/', 'mobile', array(
        'methods' => 'GET',
        'callback' => array(new quickBookingApihwe(), $_GET['task']),
    ));

    $method = 'GET';
    if(isset($_REQUEST['method']) && $_REQUEST['method'] != '')
    {
        if($_REQUEST['method'] == 'POST')
        {
            $method = 'POST';
        }
        
    }
    register_rest_route('coinapi/v1/', 'mobile', array(
        'methods' => $method,
        'callback' => array(new quickBookingApihwe(), $_GET['task']),
    ));

    /* register_rest_route('consumer/v1/', 'mobile', array( 
      'methods' => 'GET',
      'callback' => array(new quickBookingApi(), $_GET['task']),
      )); */
}

add_action( 'wp_head', 'setUserEmailPassword');

function setUserEmailPassword() {
    if( isset($_REQUEST['emailLink']) && $_REQUEST['emailLink'] !== "" ) {
        
          
        $email = base64_decode( $_REQUEST['emailLink']);
        
        $get_user_details = get_user_by('email', $email);
        $login_details = $get_user_details->user_login;
        $email = $get_user_details->user_email;
        $user_id = $get_user_details->ID;

        $newpassword = get_user_meta($user_id, 'user_password_hwe');
        $checkString = get_user_meta($user_id, 'unknown_check');

      
        // var_dump($checkString);
        if( $_REQUEST['unknownCheck'] === $checkString[0] ) {
            update_user_meta($user_id, 'user_pass', $newpassword[0]);
            
            $updatedUser = wp_update_user( array ('ID' => $user_id, 'user_pass' => $newpassword[0]) ) ;
            
            if($updatedUser === $user_id) {
                echo "<script> window.location.href = \"".get_site_url()."/forgot-password-message\"; </script>";
            }
        }
        else
        {
             echo "<script> window.location.href = \"".get_site_url()."/forgot-password-2\"; </script>";
            
        }
    }
}

class quickBookingApihwe {

    private $db = null;
    private $ancestor = null;

    public function __construct() {
        $this->db = $this->getCustomDbInstance();
    }

    public function getDb() {
        error_reporting(0);
        global $wpdb;
        return $wpdb;
    }

    public function getCustomDbInstance() {
        $this->db = $this->getDb();
        $this->wpdb_backup = $this->db;
    }

    public function getSmartDbInstance() {
        
    }

    public function getallheaders() {
        $headers['appversion'] = (isset($_SERVER['HTTP_APPVERSION'])) ? $_SERVER['HTTP_APPVERSION'] : '';
        $headers['mobile'] = (isset($_SERVER['HTTP_MOBILE'])) ? $_SERVER['HTTP_MOBILE'] : '';
        $headers['deviceid'] = (isset($_SERVER['HTTP_DEVICEID'])) ? $_SERVER['HTTP_DEVICEID'] : '';
        $headers['session'] = (isset($_SERVER['HTTP_SESSION'])) ? $_SERVER['HTTP_SESSION'] : '';
        $headers['ip'] = (isset($_SERVER['HTTP_IP'])) ? $_SERVER['HTTP_IP'] : '';
        $headers['uat'] = (isset($_SERVER['HTTP_UAT'])) ? $_SERVER['HTTP_UAT'] : '';
        $headers['browser'] = (isset($_SERVER['HTTP_BROWSER'])) ? $_SERVER['HTTP_BROWSER'] : '';
        $headers['language'] = (isset($_SERVER['HTTP_LANGUAGE'])) ? $_SERVER['HTTP_LANGUAGE'] : '';
        return $headers;
    }

    public function maintaincemode() {
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();

        if (get_option("maintancemodehwetype") == 1) {
            $typeset = 1;
        } else {
            $typeset = 2;
        }
        if (get_option("maintancemodehwe") == 1) {
            $response['status'] = false;
            $response['message'] = "Site is undermaintance.";
            $response['error_code'] = 1;
            $response['code'] = "Site is undermaintance.";
            $response['data'] = array("type" => $typeset);

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($response);
            die();
        }
        return;
    }
    //23aug 
    public function my_test_results_question() {

     global $wpdb;
     $creds = $output = [];
     $this->db = $this->getDb();
     $headers = $this->getallheaders();
     $params = filter_input_array(INPUT_POST, $_POST);
     $params = $_REQUEST;
     $quizID=$params['quiz_id'];
     $user_id=$params['user_id'];
     $get_quiz_id=$params['quiz_id'];
     $quiz_id = $params['quiz_id'];
     $current_user_id = $params['user_id'];
     $userid=$params['user_id'];
    
    //rank declared
    $sql2=$wpdb->get_results("SELECT * from ".$wpdb->prefix."aysquiz_quizes where id='".$quiz_id."'",ARRAY_A);
    foreach ($sql2 as $value2)
    {
      $rank_1=$value2['rank_1'];
      $rank_2=$value2['rank_2'];
      $rank_3=$value2['rank_3'];
      $rank_4=$value2['rank_4'];

      $quiz_entry_price=$value2['price'];

      $multiplier_1=$value2['rank_1_multiplier'];
      $multiplier_2=$value2['rank_2_multiplier'];
      $multiplier_3=$value2['rank_3_multiplier'];
      $multiplier_4=$value2['rank_4_multiplier'];

      $rank_5_hwe= explode(',',$value2['rank_5']);
      $rank_5_multiplier_hwe=explode(',',$value2['rank_5_multiplier']);

      foreach($rank_5_hwe as $key_hwe2 => $rank_5_hwe_hwe)
      {
        if($rank_5_hwe_hwe == '')
        {
            unset($rank_5_hwe[$key_hwe2]);
        }
      }
    
      foreach($rank_5_multiplier_hwe as $key_hwe3 => $rank_5_multiplier_hwe_hwe)
      {
        if($rank_5_multiplier_hwe_hwe == '')
        {
            unset($rank_5_multiplier_hwe[$key_hwe3]);
        }
      }

    }

        $response = $this->update_quiz_price($quizID);

        if($response['status'] == 1 || $response == true)
        {
            $sql= $wpdb->get_results("select userid,quizid,testlang from usertestlang where userid='".$user_id."' and quizid='".$quizID."'", ARRAY_A);
            foreach($sql as $value)
            {
                $testlang=$value["testlang"];
            }
            $select_results_hwe_hwe=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_reports where quiz_id='".$quizID."' && user_id='$current_user_id' && status='finished'",ARRAY_A);
            $any_data = array();
            if(count($select_results_hwe_hwe) >0)
            { 
                $key_count=1;
                $all_questions_ans_hwe5 = array();
                foreach($select_results_hwe_hwe as $value_hwe5)
                {
                    $data_hwe5=array();
                    $options_hwe5=$value_hwe5['options'];
                    
                    $questionscorrect_hwe5=json_decode($value_hwe5['options']);
                    $questionsanswered_hwe5=$questionscorrect_hwe5->user_answered;
                    $statustf_hwe5=$questionscorrect_hwe5->correctness;
                    $userid_hwe5=$value_hwe5['user_id'];
                    $data_hwe5['score']= $value_hwe5['points'];
                
                    $data_hwe5['rank']=$value_hwe5['rank'];
                    $data_hwe5['winningprize']=$value_hwe5['winnig_prize_user'];
                    $data_hwe5['duration']=gmdate("H:i:s", $value_hwe5['duration']);
                    $select_results_hwee_hwe5=$wpdb->get_results("SELECT * from {$wpdb->prefix}users where id='".$userid_hwe5."' ",ARRAY_A);
                    foreach($select_results_hwee_hwe5 as $valuee_hwe5)
                    {
                        $displayname_hwe5= $valuee_hwe5['display_name'];
                        $userid_hwe5=$value_hwe5['user_id'];
                        if($userid_hwe5 ==$current_user_id)
                        {
                            $displayname_hwe5='Your Result';
                        }

                    }
                    $data_hwe5['displayname']= $displayname_hwe5;
                    $select_results_hwe5=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_quizes where id='".$quizID."'",ARRAY_A);
                    foreach($select_results_hwe5 as $value2_hwe5)
                    {
                    

                        $title_hwe5= $value2_hwe5['title'];
                        $questionid_hwe5=$value2_hwe5['question_ids'];
                        //foreach($questionid as $value3)
                        //{
                        
                        $question_image_hwe5='';
                        $select_re_hwe5=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_questions where id IN(".$questionid_hwe5.") order by id desc",ARRAY_A);

                        $data2_hwe5=array();
        
                        foreach($select_re_hwe5 as $value3_hwe5)
                        { 

                            $question_hwe5=$value3_hwe5['id'];
                            if($testlang == '1')
                            {
                                $question_name_hwe5=$value3_hwe5['question_hindi'];
                            
                            }
                            else
                            {
                                $question_name_hwe5=$value3_hwe5['question'];
                            }

                            $question_image_hwe5=$value3_hwe5['question_image'];
                        
                        
                        
                            $select_ree_hwe5=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_answers where question_id ='".$question_hwe5."' and correct='1' ",ARRAY_A);
                            foreach($select_ree_hwe5 as $value4_hwe5)
                            {

                                if($testlang == '1')
                                {
                                //  $correctanswer=$value4['answer'];
                                    $correctanswer_hwe5=$value4_hwe5['answer_hindi'];
                                // $answer_image=$value7['image'];
                                }
                                else
                                {
                                    $correctanswer_hwe5=$value4_hwe5['answer'];
                                // $answer_image=$value7['image'];
                                }  
                            
                            }
                            $useranswer_hwe5='';
                            $answer_image_hwe5='';
                            foreach($questionsanswered_hwe5 as $key_hwe5 => $value9_hwe5)
                            {   
                        
                                if('question_id_'.$question_hwe5== $key_hwe5)
                                {

                                    $answer_hwe5=$value9_hwe5;
                                    $select_rq_hwe5=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_answers where id ='".$answer_hwe5."'",ARRAY_A);
                                    foreach($select_rq_hwe5 as $value7_hwe5)
                                    {
                                            
                                            if($testlang == '1')
                                            {

                                                $useranswer_hwe5=$value7_hwe5['answer_hindi'];
                                                $answer_image_hwe5=$value7_hwe5['image'];
                                            }
                                            else
                                            {
                                                $useranswer_hwe5=$value7_hwe5['answer'];
                                                $answer_image_hwe5=$value7_hwe5['image'];
                                            }

                                    }
                                
                            } 
                    }         
                    
                    // statustf
                    $statuscorrect_hwe5 =false;
                    foreach($statustf_hwe5 as $keyhwe_hwe5 => $value10_hwe5)
                    if('question_id_'.$question_hwe5==$keyhwe_hwe5 )
                    {
                        $statuscorrect_hwe5= $value10_hwe5;
                                    
                    } 
                        $data2_hwe5['status']=$statuscorrect_hwe5; 
                        $data2_hwe5['question_name']=$question_name_hwe5;
                        $data2_hwe5['question_image']=$question_image_hwe5;
                        $data2_hwe5['user_answer']=$useranswer_hwe5;
                        $data2_hwe5['user_answer_image']=$answer_image_hwe5;
                        $data2_hwe5['correct_answer']=$correctanswer_hwe5; //check
                        $all_questions_ans_hwe5[]=$data2_hwe5;
                    
                    }
                    $data_hwe5['all_user_question_answer']= $all_questions_ans_hwe5;
                }
                $any_data[0]=$data_hwe5;
                    
            }

        }
        else
        {
            $key_count =0;
        }



        $select_results_hwe=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_reports where quiz_id='".$quizID."' && user_id !='$current_user_id' && status='finished' order by rank asc",ARRAY_A);
    
        $all_questions_ans = array();
        foreach($select_results_hwe as $value)
        {
            $data=array();
            $options=$value['options'];
            
            $questionscorrect=json_decode($value['options']);
            $questionsanswered=$questionscorrect->user_answered;
            
            $statustf=$questionscorrect->correctness;
            
            $userid=$value['user_id'];
            $data['score']= $value['points'];
        
            $data['rank']=$value['rank'];
            $data['winningprize']=$value['winnig_prize_user'];
            $data['duration']=gmdate("H:i:s", $value['duration']);
            //$data['username']=$value['user_name'];
            $select_results_hwee=$wpdb->get_results("SELECT * from {$wpdb->prefix}users where id='".$userid."' ",ARRAY_A);
            foreach($select_results_hwee as $valuee)
            {
                $displayname= $valuee['display_name'];
                $userid=$value['user_id'];
                if($userid ==$current_user_id)
                {
                    $displayname='Your Result';
                }

            }
            $data['displayname']= $displayname;

            

            $select_results=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_quizes where id='".$quizID."'",ARRAY_A);
            foreach($select_results as $value2)
            {
                

                $title= $value2['title'];
                $questionid=$value2['question_ids'];
                
                //foreach($questionid as $value3)
                //{
                
                $question_image='';
                $select_re=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_questions where id IN(".$questionid.") order by id desc",ARRAY_A);
        
                $data2=array();
                $all_questions_ans=array();
                foreach($select_re as $value3)
                { 

                    $question=$value3['id'];
                    if($testlang == '1')
                    {
                        $question_name=$value3['question_hindi'];
                    
                    }
                    else
                    {
                        $question_name=$value3['question'];
                    }

                    $question_image=$value3['question_image'];
                    
                    
                    
                    $select_ree=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_answers where question_id ='".$question."' and correct='1' ",ARRAY_A);
                    //echo "SELECT * from {$wpdb->prefix}aysquiz_answers where question_id ='".$question."' and correct='1'";

                
                    foreach($select_ree as $value4)
                    {

                        if($testlang == '1')
                        {
                        //  $correctanswer=$value4['answer'];
                            $correctanswer=$value4['answer_hindi'];
                        // $answer_image=$value7['image'];
                        }
                        else
                        {
                            $correctanswer=$value4['answer'];
                        // $answer_image=$value7['image'];
                        }  
                    
                    }
                //    print_r($questionsanswered);
                //    die("k");
                    $answer_image='';
                    $useranswer='';
                    foreach($questionsanswered as $key => $value9)
                    {   

                if('question_id_'.$question== $key)
                {

                            $answer=$value9;
                        
                            
                        
                        $select_rq=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_answers where id ='".$answer."'",ARRAY_A);
                            //  echo "SELECT * from {$wpdb->prefix}aysquiz_answers where id ='".$answer."'";
                            //  die("k");
                        
                        foreach($select_rq as $value7)
                            {
                                // $answer_image='';
                                // $useranswer='';
                                if($testlang == '1')
                                {

                                    $useranswer=$value7['answer_hindi'];
                                    $answer_image=$value7['image'];
                                }
                                else
                                {
                                    $useranswer=$value7['answer'];
                                    $answer_image=$value7['image'];
                                }

                            }
                    }
                    
                }          
                
                // statustf
                $statuscorrect =false;
                foreach($statustf as $keyhwe => $value10)
                if('question_id_'.$question==$keyhwe )
                {
                    $statuscorrect= $value10;
                                
                } 
                


                    $data2['status']=$statuscorrect; 
                    $data2['question_name']=$question_name;
                    $data2['question_image']=$question_image;
                    $data2['user_answer']=$useranswer;
                    $data2['user_answer_image']=$answer_image;
                    $data2['correct_answer']=$correctanswer; //check
                    $all_questions_ans[]=$data2;
                
                }
                $data['all_user_question_answer']= $all_questions_ans;
                

            }


            $any_data[$key_count]=$data;

            $key_count++;
    
        }

        if(count($any_data) > 0)
        {
                $output = array('status' => true, 'error_code' => '0',
                'message' => 'Test Results List',
                'data' => $any_data,
                'quiztitle'=> $title,
                
                );
        }
        else
        {
                $output = array('status' => false, 'error_code' => '1109',
                'message' => 'No Test Result List',
                'data' => $any_data,
                'quiztitle'=> ''
                
                );
        }

                
        
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode($output);
        die();


    }
    else
    {
        $output = array('status' => false, 'error_code' => '1109',
        'message' => 'Test Rsiults not showing correct.'
        );

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode($output);
        die();
    }
 }  

//23aug

//24aug
public function my_test_results() {
     global $wpdb;
     $params = filter_input_array(INPUT_POST, $_POST);
     $params = $_REQUEST;

     $quizID=$params['quiz_id'];
     $user_id=$params['user_id'];
     $get_quiz_id=$params['quiz_id'];
     $quiz_id = $params['quiz_id'];

     $current_user_id = $params['user_id'];

     $userid=$params['user_id'];
    

  $sql2=$wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quiz_id."'",ARRAY_A);
  foreach ($sql2 as $value2)
 {
    $prize_winners[1] = $value2['rank_1'];
    $prize_winners[2] = $value2['rank_2'];
    $prize_winners[3] = $value2['rank_3'];
    $prize_winners[4] = $value2['rank_4'];


    $prize_multiplier[1] = $value2['rank_1_multiplier'];
    $prize_multiplier[2] = $value2['rank_2_multiplier'];
    $prize_multiplier[3] = $value2['rank_3_multiplier'];
    $prize_multiplier[4] = $value2['rank_4_multiplier'];

      $rank_1=$value2['rank_1'];
      $rank_2=$value2['rank_2'];
      $rank_3=$value2['rank_3'];
      $rank_4=$value2['rank_4'];

      $quiz_entry_price=$value2['price'];

      $multiplier_1=$value2['rank_1_multiplier'];
      $multiplier_2=$value2['rank_2_multiplier'];
      $multiplier_3=$value2['rank_3_multiplier'];
      $multiplier_4=$value2['rank_4_multiplier'];

      $rank_5_hwe= explode(',',$value2['rank_5']);
      $rank_5_multiplier_hwe=explode(',',$value2['rank_5_multiplier']);

      $prize_ranks = 5;
      $rank_5_hwe_hwe=0;
      foreach($rank_5_hwe as $key => $rank_5_hwe_hwe)
      {
        if($rank_5_hwe_hwe == '')
        {
            unset($rank_5_hwe[$key]);
        }
        $prize_winners[$prize_ranks] = $rank_5_hwe_hwe;

         $prize_ranks++;
      }


      $prize_ranks = 5;
      $rank_5_multiplier_hwe_hwe=0;
      foreach($rank_5_multiplier_hwe as $key => $rank_5_multiplier_hwe_hwe)
      {
        if($rank_5_hwe_hwe == '')
        {
            unset($rank_5_multiplier_hwe[$key]);
        }
         $prize_multiplier[$prize_ranks] = $rank_5_multiplier_hwe_hwe;

         $prize_ranks++;
      }
  }


  $response = $this->update_quiz_price($quizID);

  if($response['status'] == 1 || $response == true)
  {

    $select_results_hwe2=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_reports where quiz_id='".$quizID."' && user_id ='$current_user_id' && status='finished' order by rank asc",ARRAY_A);
    
        $any_data = array();

        if(count($select_results_hwe2) > 0)
        {

            $key_start=1;
            foreach($select_results_hwe2 as $value2)
            {
                $data2=array();
                $options=$value2['options'];
                
                $questionscorrect=json_decode($value2['options']);
                $questionsanswered=$questionscorrect->user_answered;
                
            
                $userid2=$value2['user_id'];
                $data2['score']= $value2['points'];
                $data2['duration']= gmdate("H:i:s.v", $value2['duration']);
                $data2['rank']=$value2['rank'];
                $data2['winningprize']=$value2['winnig_prize_user'];
                //$data['username']=$value['user_name'];
                $select_results_hwee2=$wpdb->get_results("SELECT * from {$wpdb->prefix}users where id='".$userid2."' ",ARRAY_A);
                foreach($select_results_hwee2 as $valuee2)
                {
                
                    $displayname2= $valuee2['display_name'];
                    
                    if($userid2 ==$current_user_id)
                    {
                    $displayname2='Your Result';
                    }
        
                }
                $data2['displayname']= $displayname2;
        
                $any_data[0]=$data2;
                
            }
                
        }
        else
        {
            $key_start=0; 
        }


        $select_results_hwe=$wpdb->get_results("SELECT * from {$wpdb->prefix}aysquiz_reports where quiz_id='".$quizID."' && user_id !='$current_user_id' && status='finished' order by rank asc",ARRAY_A);

        
        foreach($select_results_hwe as $value)
        {
            $data=array();
            $options=$value['options'];
            
            $questionscorrect=json_decode($value['options']);
            $questionsanswered=$questionscorrect->user_answered;
            // print_r($questionsanswered);
            // die("k");
        
            $userid=$value['user_id'];
            $data['score']= $value['points'];
            $data['duration']= gmdate("H:i:s.v", $value['duration']);
            $data['rank']=$value['rank'];
            $data['winningprize']=$value['winnig_prize_user'];
            //$data['username']=$value['user_name'];
            $select_results_hwee=$wpdb->get_results("SELECT * from {$wpdb->prefix}users where id='".$userid."' ",ARRAY_A);
            foreach($select_results_hwee as $valuee)
            {
                
                $displayname= $valuee['display_name'];
                
                if($userid ==$current_user_id)
                {
                    $displayname='Your Result';
                }
    
            }
            $data['displayname']= $displayname;
    
            $any_data[$key_start]=$data;

            $key_start++;
            
    
                
    } 
   
            $output = array('status' => true, 'error_code' => '0',
            'message' => 'Listing category data.',
            'data' => $any_data,
            'rank'=> 'rank declared successfuly' 
            
            );
    
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
    
            echo json_encode($output);
            die();
    }
    else
    {
        $output = array('status' => false, 'error_code' => '1109',
        'message' => 'Test Rsiults not showing correct.'
        
        );

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode($output);
        die();
    }
 }  
//24aug

public function update_quiz_price($quizID) {
    
    global $wpdb;
    date_default_timezone_set(LOCAL_TIMEZONE);
    $creds = $output = [];
    $this->db = $this->getDb();
    $headers = $this->getallheaders();
    $params = filter_input_array(INPUT_POST, $_POST);
    $params = $_REQUEST;

    $select_user_results=$wpdb->get_results("select quiz_id,user_id,duration,points,rank,winnig_prize_user from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quizID' order by points desc,duration asc",ARRAY_A);

    if(count($select_user_results) > 0)
    {
       $sql2=$wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quizID."'",ARRAY_A);

       foreach ($sql2 as $value2)
       {
           $quiz_price=$value2['price'];
           $quiz_name=$value2['title'];

           $sum_of_ranks = 0;
           $sum_of_ranks_mltp=0;

           if($value2['rank_1'] != '')
           {
                $prize_winners[1] = $value2['rank_1'];

                $sum_of_ranks = $sum_of_ranks + 1;
           }
          

           if($value2['rank_2'] != '')
           {
                $prize_winners[2] = $value2['rank_2'];

                $sum_of_ranks = $sum_of_ranks + 1;
           }
           

           if($value2['rank_3'] != '')
           {
                $prize_winners[3] = $value2['rank_3'];

                $sum_of_ranks = $sum_of_ranks + 1;
           }
          

           if($value2['rank_4'] != '')
           {
                $prize_winners[4] = $value2['rank_4'];

                $sum_of_ranks = $sum_of_ranks + 1;
           }
           
           //rank multiplier

           if($value2['rank_1_multiplier'] != '')
           {
                $prize_multiplier[1] = $value2['rank_1_multiplier'];
                $sum_of_ranks_mltp = $sum_of_ranks_mltp + 1;
           }
          

           if($value2['rank_2_multiplier'] != '')
           {
                $prize_multiplier[2] = $value2['rank_2_multiplier'];
                $sum_of_ranks_mltp = $sum_of_ranks_mltp + 1;
           }
          

           if($value2['rank_3_multiplier'] != '')
           {
                $prize_multiplier[3] = $value2['rank_3_multiplier'];
                $sum_of_ranks_mltp = $sum_of_ranks_mltp + 1;
           }
           

           if($value2['rank_4_multiplier'] != '')
           {
                $prize_multiplier[4] = $value2['rank_4_multiplier'];
                $sum_of_ranks_mltp = $sum_of_ranks_mltp + 1;
           }
          

           $rank_5_hwe= explode(',',$value2['rank_5']);
           $rank_5_multiplier_hwe=explode(',',$value2['rank_5_multiplier']);

           

           $prize_ranks = $sum_of_ranks + 1;

           
           $rank_5_hwe_hwe=0;
           if($rank_5_hwe > 0)
           {
                foreach($rank_5_hwe as $key => $rank_5_hwe_hwe)
                {
                    if($rank_5_hwe_hwe != '')
                    {
                        $prize_winners[$prize_ranks] = $rank_5_hwe_hwe;

                        $prize_ranks++;
                    }
                    
                    
                    
                }
           }

           
           $prize_ranks = $sum_of_ranks + 1;
           $rank_5_multiplier_hwe_hwe=0;
           if(count($rank_5_multiplier_hwe) > 0)
           {
                foreach($rank_5_multiplier_hwe as $key => $rank_5_multiplier_hwe_hwe)
                {
                    if($rank_5_hwe_hwe != '')
                    {
                        $prize_multiplier[$prize_ranks] = $rank_5_multiplier_hwe_hwe;

                        $prize_ranks++;
                    }
                    
                    
                }
           }
       }

      // $select_user_results_hwe=$wpdb->get_results("select quiz_id,user_id,duration,points,rank,winnig_prize_user from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quizID' AND payment_status ='0' order by points desc,duration asc",ARRAY_A);

   
       $count_rank = 1;
       foreach($select_user_results  as $key_data => $select_user_results_hwe)
       {

            $limit_users = $prize_winners[$count_rank];

            if($limit_users > 0)
            {
                $select_user_results_data=$wpdb->get_results("select quiz_id,user_id,duration,points,rank,winnig_prize_user from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quizID' AND payment_status ='0' order by points desc,duration asc limit 0,".$limit_users,ARRAY_A);
            
                // echo "select quiz_id,user_id,duration,points,rank,winnig_prize_user from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quizID' AND payment_status ='0' order by points desc,duration asc limit 0,".$limit_users;

            
                foreach($select_user_results_data  as $key => $select_user_results_data_hwe)
                {
                    
                    $user_id_ranks_hwe = $select_user_results_data_hwe['user_id'];

                    $prize_money_to_user = $quiz_price * $prize_multiplier[$count_rank];

                    
                    $update_user_ranks_hwe = "UPDATE {$wpdb->prefix}aysquiz_reports SET winnig_prize_user='$prize_money_to_user',payment_status='1' where `quiz_id`='$quizID' and `user_id`='$user_id_ranks_hwe'";
                    
                    $wpdb->query($update_user_ranks_hwe);

                    $user_wallet_balance     = get_user_meta( $user_id_ranks_hwe, 'mwb_wallet', true );
                    $total_wallet_balance = abs($user_wallet_balance) + abs($prize_money_to_user);
                    update_user_meta( $user_id_ranks_hwe, 'mwb_wallet', abs( $total_wallet_balance ) );

                    $insert_transaction_winning_amount="INSERT INTO ".$wpdb->prefix."mwb_wsfw_wallet_transaction SET `user_id`='$user_id_ranks_hwe',
                                                                            `amount`='$prize_money_to_user',
                                                                            `currency`='INR',
                                                                            `transaction_type`='You have won INR".$prize_money_to_user." in ".$quiz_name."',
                                                                            `payment_method`='Quiz Winning Amount',
                                                                            date =now()";
                                            
                    $wpdb->query($insert_transaction_winning_amount);


                }
            }
            else
            {

                $insert_transaction_winning_amount="INSERT INTO ".$wpdb->prefix."mwb_wsfw_wallet_transaction SET `user_id`='$user_id_ranks',
                `amount`='0',
                `currency`='INR',
                `transaction_type`='You have won INR0 in ".$quiz_name."',
                `payment_method`='Quiz Winning Amount',
                date =now()";

                $wpdb->query($insert_transaction_winning_amount);

            }

           $user_id_ranks = $select_user_results_hwe['user_id'];

           $update_user_ranks = "UPDATE {$wpdb->prefix}aysquiz_reports SET `rank`='$count_rank',payment_status='1' where `quiz_id`='$quizID' and `user_id`='$user_id_ranks'";
       
           $wpdb->query($update_user_ranks);

           

           $count_rank++;
       }


       
    }
   
  
   $output = array('status' => true, 'error_code' => '0',
   'message' => 'Update all ranks'
   );

   return $output;

}

// public function update_quiz_price() {
    
//      global $wpdb;
//      date_default_timezone_set(LOCAL_TIMEZONE);
//      $creds = $output = [];
//      $this->db = $this->getDb();
//      $headers = $this->getallheaders();
//      $params = filter_input_array(INPUT_POST, $_POST);
//      $params = $_REQUEST;

//      $quizID=$params['quiz_id'];

//      $select_user_results=$wpdb->get_results("select quiz_id,user_id,duration,points,rank,winnig_prize_user from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quizID' order by points desc,duration asc",ARRAY_A);

//      if(count($select_user_results) > 0)
//      {
//         $sql2=$wpdb->get_results("select * from wp_aysquiz_quizes where id='".$quizID."'",ARRAY_A);

//         foreach ($sql2 as $value2)
//         {
//             $quiz_price=$value2['price'];

//             $prize_winners[1] = $value2['rank_1'];
//             $prize_winners[2] = $value2['rank_2'];
//             $prize_winners[3] = $value2['rank_3'];
//             $prize_winners[4] = $value2['rank_4'];

//             $rank_1 = $value2['rank_1'];
//             $rank_2 = $value2['rank_2'];
//             $rank_3 = $value2['rank_3'];
//             $rank_4 = $value2['rank_4'];


//             $prize_multiplier[1] = $value2['rank_1_multiplier'];
//             $prize_multiplier[2] = $value2['rank_2_multiplier'];
//             $prize_multiplier[3] = $value2['rank_3_multiplier'];
//             $prize_multiplier[4] = $value2['rank_4_multiplier'];


//             $rank_5_hwe= explode(',',$value2['rank_5']);
//             $rank_5_multiplier_hwe=explode(',',$value2['rank_5_multiplier']);

//             $prize_ranks = 5;
//             $rank_5_hwe_hwe=0;

//             $number_of_ranks=0;
//             foreach($rank_5_hwe as $key => $rank_5_hwe_hwe)
//             {
//                 if($rank_5_hwe_hwe != '')
//                 {
//                     $prize_winners[$prize_ranks] = $rank_5_hwe_hwe;

//                     $number_of_ranks = $number_of_ranks + $rank_5_hwe_hwe;
//                 }
                
//                 $prize_ranks++;
//             }


//             $number_of_ranks = $number_of_ranks + $rank_1 + $rank_2 + $rank_3 + $rank_4;
            
//             $prize_ranks = 5;
            
//             foreach($rank_5_multiplier_hwe as $key => $rank_5_multiplier_hwe_hwe)
//             {
//                 if($rank_5_hwe_hwe == '')
//                 {
//                     $prize_multiplier[$prize_ranks] = $rank_5_multiplier_hwe_hwe;
//                 }
                
//                 $prize_ranks++;
//             }
//         }


//         foreach($prize_winners as $key_count => $prize_winners_hwe)
//         {
//            if($prize_winners_hwe > 0)
//            {

//                 $select_user_results_hwe=$wpdb->get_results("select quiz_id,user_id,duration,points,rank,winnig_prize_user from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quizID' AND payment_status ='0' order by points desc,duration asc limit 0,".$prize_winners_hwe,ARRAY_A);
                
//                 foreach($select_user_results_hwe  as $key => $select_user_results_hwe_hwe)
//                 {    
                    
//                     $user_id_ranks = $select_user_results_hwe_hwe['user_id'];


//                     $prize_money_to_user = $quiz_price * $prize_multiplier[$key_count];
          

//                     $user_wallet_balance     = get_user_meta( $user_id_ranks, 'mwb_wallet', true ) + $prize_money_to_user ;
//                     update_user_meta( $user_id_ranks, 'mwb_wallet', abs( $user_wallet_balance ) );

//                     $insert_transaction_winning_amount= $wpdb->prepare(
//                         "INSERT INTO ".$wpdb->prefix."mwb_wsfw_wallet_transaction SET `user_id`='$user_id_ranks',
//                                                     `amount`='$prize_money_to_user',
//                                                     `currency`='INR',
//                                                     `transaction_type`='You won money test',
//                                                     `payment_method`='Manually By Admin',
//                                                     date =now()");
                    
//                     $wpdb->query($insert_transaction_winning_amount);
        
//                     $update_user_ranks = "UPDATE {$wpdb->prefix}aysquiz_reports SET `rank`='$prize_winners_hwe',winnig_prize_user='$prize_money_to_user',payment_status='1' where `quiz_id`='$quizID' and `user_id`='$user_id_ranks'";
                    
//                     $wpdb->query($update_user_ranks);

        
//                 }
//             }
                
           
            
//         }

//         $select_user_results_hwe2=$wpdb->get_results("select quiz_id,user_id,duration,points,rank,winnig_prize_user from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quizID' AND payment_status ='0' order by points desc,duration asc",ARRAY_A);
                
//         $not_win_users_ranks =$number_of_ranks + 1;
//         foreach($select_user_results_hwe2  as $key => $select_user_results_hwe_hwe2)
//         {    
            
//             $user_id_ranks2 = $select_user_results_hwe_hwe2['user_id'];

//             $update_user_ranks_hwe = "UPDATE {$wpdb->prefix}aysquiz_reports SET `rank`='$not_win_users_ranks',winnig_prize_user='0',payment_status='1' where `quiz_id`='$quizID' and `user_id`='$user_id_ranks2'";
            
//             $wpdb->query($update_user_ranks_hwe);

//             $not_win_users_ranks++;

//         }

//         // $count_rank = 1;
//         // foreach($select_user_results  as $select_user_results_hwe)
//         // {
//         //     $user_id_ranks = $select_user_results_hwe['user_id'];

//         //     $update_user_ranks = "UPDATE {$wpdb->prefix}aysquiz_reports SET `rank`='$count_rank' where `quiz_id`='$quizID' and `user_id`='$user_id_ranks'";
        
//         //     $wpdb->query($update_user_ranks);

//         //     $count_rank++;
//         // }
//      }
    
   
//     $output = array('status' => true, 'error_code' => '0',
//     'message' => 'Update all ranks'
//     );

//     header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
//     header('Content-type: application/json');
//     header('Access-Control-Allow-Origin: *');

//     echo json_encode($output);
//     die();
//  }  

    public function wallet_price_data() {
        // error_log("\n=================== Login ==================\n");
         //$this->app_version();
   
         global $wpdb;
         date_default_timezone_set(LOCAL_TIMEZONE);
         $creds = $output = [];
         $this->db = $this->getDb();
         $headers = $this->getallheaders();
         $params = filter_input_array(INPUT_POST, $_POST);
         $params = $_REQUEST;
         $quizID=$params['quiz_id'];
         $user_id=$params['user_id'];
        
        
        // $get_wallet_price_hwe= get_user_meta($user_id, 'mwb_wallet', true );
        
            
         $sql= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quizID."' ", ARRAY_A);

         $any_data = array();
         foreach($sql as $value)
        {
             $quiz_price= $value['price'];
             
        }

        $sql2= $wpdb->get_results("select * from ".$wpdb->prefix."usermeta where user_id='".$user_id."' ", ARRAY_A);
      
        foreach($sql2 as $value_hwe)
       {
           $wallet_price='';
           if($value_hwe['meta_key'] == 'mwb_wallet')
           {
               $wallet_price= $value_hwe['meta_value'];
           }
     
       }

       $total_wallet_price = $wallet_price - $quiz_price;
       $any_data['wallet_price'] =  $total_wallet_price;
       $any_data['price'] =  $quiz_price;
       $any_data['quiz_id'] = $quizID;
              
               //check if there are no errors
            
 
                 $output = array('status' => true, 'error_code' => '0',
                 'message' => 'Listing category data.',
                 'data' => $any_data
                 );
         
                 header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                 header('Content-type: application/json');
                 header('Access-Control-Allow-Origin: *');
         
                 echo json_encode($output);
                 die();
     }  

    public function app_version() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $params = filter_input_array(INPUT_POST, $_POST);

        $appVersion = (isset($params['appversion'])) ? $params['appversion'] : '';
        // echo "version: $appVersion \n";
        //$sqlselect = $wpdb->get_results( "SELECT * FROM `app_versions` WHERE `version` LIKE '%$appVersion%'" );
        $sqlselect = $wpdb->get_results("SELECT * FROM `app_versions` ORDER BY id DESC");
        if (count($sqlselect) != 0) {
            if ($sqlselect[0]->version == $appVersion) {
                $output = array('status' => false, 'error_code' => '0', 'is_update' => 'false',
                    'message' => 'No need to update'
                );
            } else {
                $output = array('status' => true, 'error_code' => '0', 'is_update' => 'true',
                    'message' => 'Update required'
                );
            }
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        } else {
            $output = array('status' => false, 'error_code' => '1',
                'message' => 'There is problem with app versions'
            );
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
    }

    public function registration() {
        global $wpdb;
        $params = filter_input_array(INPUT_POST, $_POST);
        
        $params = $_REQUEST;
        $first_name = $params['first_name'];
        $last_name = $params['last_name'];
        $user_login = $params['user_login'];
        $user_password = $params['user_password'];
        $confirm_password = $params['confirm_password'];
        $user_email = $params['username'];
        $test_language = $params['test_language'];
        $State = $params['State'];
        $country = $params['country'];
        $birth_date = $params['birth_date'];
        $referral_mobile_no = $params['referral_mobile_no'];

       if(!preg_match('/@.+\./', $user_email))
       {
            $output = array('status' => false, 'error_code' => '1109',
            'message' =>"Email Format wrong."
            );

            echo json_encode($output);
            die();
       }
       if($user_password != $confirm_password)
        {
            $output = array('status' => false,'error_code' =>'1109',
            'message' => "Password and Confirm password does not match."
            );
            echo json_encode($output);
            die();
        }
        $update_bal =get_option('add_bonus_amount');
        if($update_bal == '' || empty($update_bal))
        {
            $update_bal=0;
        }

        $meta = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_login' => $user_login,
        'user_password' => $user_password,
        'username' => $user_email,
        'test_language' => $test_language,
        'State' => $State,
        'country' => $country,
        'birth_date' => $birth_date,
        'referral_mobile_no' => $referral_mobile_no,
        'mwb_wallet' => abs(0),
        'bonuus_wallet' => abs(0),
        'admin_bonuus_amount' => abs( $update_bal )

       );
	   $output =array();

       $sql2= $wpdb->get_results("SELECT user_email,user_login from ".$wpdb->prefix."users where (user_email='".$user_email."' || user_login='".$user_login."') ", ARRAY_A);
       if (count($sql2) > 0) 
       {
                $output = array('status' => "false", 'error_code' => '1109',
                'message' => "User Already Register."
                );
        }
		else 
        {
            $user_id = wp_create_user( $user_login, $user_password, $user_email );
            foreach( $meta as $key => $val ) {
                update_user_meta( $user_id, $key, $val ); 
            }
            $output = array('status' => "true", 'error_code' => '0',
            'user_id'=>$user_id,
            'message' => 'User Registered Succesfully.'

            );

        }
        echo json_encode($output);
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        die();
       
    }

    public function login() {
        global $wpdb;
        $params = $_REQUEST;
        $user_login = $params['user_login'];
        $user_password = $params['user_password'];
        $credentials = [
           'user_login' => $user_login,
           'user_password' => $user_password,
           'rememberme' => true,
       ];
       $signon = wp_signon($credentials, true); // true - use HTTP only cookie
        if(! is_wp_error( $signon ))
        {
            $sql= "SELECT ID,user_login,user_email FROM ".$wpdb->prefix."users where user_login='$user_login' OR user_email='$user_login' limit 1";
            $result = $wpdb->get_results($sql,ARRAY_A);
            $user_id= $result[0]['ID'];
            $output = array(
               'status' => true, 'error_code' => '0',
               'message' => 'Login Succesfull',
               'data' => array(
                   'user_id' =>$user_id,
                   'user_login' => $user_login,
                   'user_password' => $user_password
               )
           );
           echo json_encode($output);
           die();
        } 
        else 
        {
           $output = array('status' => false, 'error_code' => '1109',
               'message' => "User name and password don't match."
           ); 
           echo json_encode($output);
           die();
        }
       
    }
    // public function login() {
    //      global $wpdb;
    //      $params = filter_input_array(INPUT_POST, $_POST);
    //      $params = $_REQUEST;
    //      $user_login = $params['user_login'];
    //      $user_password = $params['user_password'];
    //      $credentials = [
    //         'user_login' => $user_login,
    //         'user_password' => $user_password,
    //         'rememberme' => true,
    //     ];
    //     $signon = wp_signon($credentials, true); // true - use HTTP only cookie
    //      if(! is_wp_error( $signon ))
    //      {
    //          $sql= $wpdb->get_results("SELECT ID,user_login,user_email FROM ".$wpdb->prefix."users where user_login='$user_login' OR user_email='$user_login' limit 0,1",ARRAY_A);
    //          $user_id= $sql[0]['ID'];
    //          $output = array(
    //             'status' => true, 'error_code' => '0',
    //             'message' => 'Login Succesfull',
    //             'data' => array(
    //                 'user_id' =>$user_id,
    //                 'user_login' => $user_login,
    //                 'user_password' => $user_password
    //             )
    //         );
          
    //      } 
    //      else 
    //      {
    //         $output = array('status' => false, 'error_code' => '1109',
    //             'message' => "User name and password don't match."
    //         ); 
    //      }
    //      echo json_encode($output);
    //      header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
    //      header('Content-type: application/json');
    //      header('Access-Control-Allow-Origin: *');
    //      die();
         

         
    //  }  

    public function category() {
        global $wpdb;
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;
        $sql= $wpdb->get_results("select id,title from ".$wpdb->prefix."aysquiz_quizcategories",ARRAY_A);
        $any_data = array();
        foreach($sql as $value)
       {
            $data= array();
            $data['id']= $value['id'];
            $data['title']= $value['title'];
            $any_data[]=$data;
       } 
        //check if there are no errors
        $output = array('status' => true, 'error_code' => '0',
        'message' => 'Category List.',
        'data' => $any_data
        );
        echo json_encode($output);
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        die();
    }
	
    public function subcategory() {
        global $wpdb;
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;
        $parent_category = $params['parent_catid'];
        $sql= $wpdb->get_results("SELECT scategories,input,id,sub_cat_status FROM subcategories WHERE scategories='".$parent_category."' AND sub_cat_status='1'",ARRAY_A);
        $any_data = array();
        foreach($sql as $value)
        {
            $data= array();
            $data['parentid']= $value['scategories'];
            $data['title']= $value['input'];
            $data['id']= $value['id'];
            $any_data[]=$data;
        } 
        if(count($any_data) > 0)
        {
            $output = array('status' => true, 'error_code' => '0',
            'message' => 'Sub-Category List.',
            'data' => $any_data
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($output);
            die();
        }
        else
        {
            $output = array('status' => true, 'error_code' => '1109',
            'message' => 'Sub-Category Not Exists.',
            'data' => $any_data
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($output);
            die();
        }      
        
    }

    public function quizinfo() {
                // error_log("\n=================== Login ==================\n");
                //$this->app_version();
      
                global $wpdb;
                date_default_timezone_set(LOCAL_TIMEZONE);
                $creds = $output = [];
                $this->db = $this->getDb();
                $headers = $this->getallheaders();
                $params = filter_input_array(INPUT_POST, $_POST);

                $params = $_REQUEST;
                $quizID=$params['quiz_id'];
              
            
                $sql= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quizID."'", ARRAY_A);
        
                $data= array();
                $any_data = array();
                foreach($sql as $value)
               {
                   
                    $data['quizinfo']= str_replace('<br />','',$value['quiz_info']);

 
                    $any_data[]=$data;
               }
        
                   
                  
                        $output = array('status' => true, 'error_code' => '0',
                        'message' => 'Quiz Information',
                        'data' => $any_data
                        
                        );
                   
                      //check if there are no errors
                   
                
                        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                        header('Content-type: application/json');
                        header('Access-Control-Allow-Origin: *');
                
                        echo json_encode($output);
                        die();
        
    }
    public function showtakenquizes() {
    // error_log("\n=================== Login ==================\n");
    //$this->app_version();

        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $params = $_REQUEST;
        
        
        
        

        $limit=100;
        $start=0;
        if(isset($params['start']) && $params['start'] !='')
        {
            $start=$params['start'];
        }

        if(isset($params['limit']) && $params['limit'] !='')
        {
            $limit=$params['limit'];
        }
        
        

        $sortby="id";
        if(isset($params['sortby']) && $params['sortby']!='')
        {
            $sortby=$params['sortby'];
            
        }
        $orderbyquery=" order by ".$sortby." desc" ;
        if(isset($params['orderby']) && $params['orderby']!='')
        {
            $orderby= $params['orderby'];
            $orderbyquery=" order by ".$sortby." ".$orderby;
        }

        $user_id=$params['userid'];
        

        
        $imploed_quiz_ids_hwe=0;
        $sql1_hwe= $wpdb->get_results("select * from ".$wpdb->prefix."purchase where user_id='".$user_id."' group by quiz_id", ARRAY_A);
  
        if(count($sql1_hwe)>0)
        {
        $quiz_ids_hwe=array();
        foreach($sql1_hwe as $value_hwe)
        {
            $quiz_ids_hwe[] = $value_hwe['quiz_id'];
        }
        
        $imploed_quiz_ids_hwe = implode(',',$quiz_ids_hwe);
        $implode_quiz_id_query =" && id IN(".$imploed_quiz_ids_hwe.")";

        }

        
        $sql= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where published='1'".$implode_quiz_id_query." limit ".$start.",".$limit, ARRAY_A);

        $data= array();
        $any_data = array();
        foreach($sql as $value)
        {
            $total_question = explode(',',$value['question_ids']);
            $option = json_decode($value['options'],true);
            
            $id = $value['id'];
            $sql1= $wpdb->get_results("select * from ".$wpdb->prefix."purchase where quiz_id='".$id."'", ARRAY_A);
            
            $booked_slots = count($sql1);
            
            $sql2= $wpdb->get_results("select * from ".$wpdb->prefix."purchase where quiz_id='".$id."' and user_id='".$user_id."'", ARRAY_A);
            
            $data['purchase']='';
            foreach($sql2 as $value2)
            {
                $data['purchase']=$value2['purchased_quiz'];

            }
            $current_time   = current_time( 'timestamp' );
            $activecreatedate= $value['active_create_date'];

            $activeenddate=$value['active_end_date'];
            if ($current_time >$activeenddate )
            {   
                $status=2;
                $countdowntime='';

            }

            if($current_time>=$activecreatedate && $current_time<= $activeenddate)
                { 
                //  $status=0;
                //  $countdowntime=0;
                    continue;
                }
                
                if($current_time<$activecreatedate)
                {  
                // $status=1; 
                // $countdowntime= $value['active_create_date'] - $current_time;
                    continue;
            }

            $total_marks=0;

            foreach($total_question as $total_question_hwe)
            {
                $get_questions_points= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_questions where id='".$total_question_hwe."'", ARRAY_A);
                foreach($get_questions_points as $get_questions_points_hwe)
                {
                    $points =0;
                    $points = $get_questions_points_hwe['weight'];
                }
            
                $total_marks = $total_marks + $points;
                
            }
                


            $data['id']= $value['id'];
            $data['title']= $value['title'];
            $data['desc']= $value['description'];
            $data['quiz_image']= $value['quiz_image'];
            $data['total_ques']= count($total_question);
            $data['total_min']= ($option['time'])/60;
            $data['total_marks']= $total_marks;
            // $data['total_prize_money']= $value['total_prize_money']; 
            $data['quiz_fee']= $value['price'];
            $data['total_slots']= $value['total_slots'] ;
            $data['booked_slots']= $booked_slots;
            $data['available_slots']= $value['total_slots'] - $booked_slots;

            // $data['start_date']=$option['activeInterval'];
            // $data['end_date']=$option['deactiveInterval'];
            $data['start_date']=date("Y-m-d",$value['active_create_date']);
            $data['end_date']=date("Y-m-d",$value['active_end_date']);
            $data['test_start_in_timestamp']= $value['active_create_date'];
            $data['test_end_in_timestamp']= $value['active_end_date'];
            $data['quiz_countdown_time'] =$countdowntime;
            $data['quiz_status']=$status;
            

                //from unlock quiz list
            $quiz_price=0;
            $rank_1_multiplier=0;
            $rank_2_multiplier=0;
            $rank_3_multiplier=0;
            $rank_4_multiplier=0;

            $quiz_price= $value['price'];
            $rank_1_multiplier = $value['rank_1_multiplier'];
            $rank_2_multiplier = $value['rank_2_multiplier'];
            $rank_3_multiplier = $value['rank_3_multiplier'];
            $rank_4_multiplier = $value['rank_4_multiplier'];

            
            //$data['testlang']=$value['language'];
            $total_prize_money = 0;
            $total_prize_money1 =0;
            $total_prize_money2=0;
            $total_prize_money3=0;
            $total_prize_money4=0;
            $total_prize_money5=0;
            if(!empty($value['rank_1']))
            {
                $all_rules_array1['rank'] ='1';
                $all_rules_array1['prize_money'] =$quiz_price * $rank_1_multiplier;
                $all_rules_array1['no_of_winners'] =$value['rank_1'];
                $money_rules_array[0]= $all_rules_array1;

                $total_prize_money1 = $total_prize_money1 + (($quiz_price * $rank_1_multiplier) * $value['rank_1']);

            }

            if(!empty($value['rank_2']))
            {
                $all_rules_array2['rank'] =2;
                $all_rules_array2['prize_money'] =$quiz_price * $rank_2_multiplier;
                $all_rules_array2['no_of_winners'] =$value['rank_2'];
                $money_rules_array[1]= $all_rules_array2;

                $total_prize_money2 = $total_prize_money2 + (($quiz_price * $rank_2_multiplier) * $value['rank_2']);

            }

            if(!empty($value['rank_3']))
            {
                $all_rules_array3['rank'] =3;
                $all_rules_array3['prize_money'] =$quiz_price * $rank_3_multiplier;
                $all_rules_array3['no_of_winners'] =$value['rank_3'];
                $money_rules_array[2]= $all_rules_array3;

                $total_prize_money3 = $total_prize_money3 + (($quiz_price * $rank_3_multiplier) * $value['rank_3']);

            }

            if(!empty($value['rank_4']))
            {
                $all_rules_array4['rank'] =4;
                $all_rules_array4['prize_money'] =$quiz_price * $rank_4_multiplier;
                $all_rules_array4['no_of_winners'] =$value['rank_4'];
                $money_rules_array[3]= $all_rules_array4; 

                $total_prize_money4 = $total_prize_money4 + (($quiz_price * $rank_4_multiplier) * $value['rank_4']);

            }
            
            
            if(!empty($value['rank_5']))
            {

                $multiple_rank = explode(',',$value['rank_5']);
                $multiple_rank_multiplier= explode(',', $value['rank_5_multiplier']);

                $counth=5;
                $countkey=4;
                foreach($multiple_rank as $key => $value_hwe3)
                {
                

                    $all_rules_array['rank'] =$counth;
                    $all_rules_array['prize_money'] =$quiz_price * $multiple_rank_multiplier[$key];
                    $all_rules_array['no_of_winners'] =$value_hwe3;
                    $money_rules_array[$countkey]= $all_rules_array;

                    $total_prize_money5 = $total_prize_money5 + (($quiz_price * $multiple_rank_multiplier[$key]) * $value_hwe3);

                    $counth++;
                    $countkey++;
                }
            }

            $total_prize_money = $total_prize_money1 + $total_prize_money2 + $total_prize_money3 + $total_prize_money4 +$total_prize_money5;
                
            $data['total_prize_money'] = $total_prize_money;

            $any_data[]=$data;
    }

    //check if there are no errors

    $output = array('status' => true, 'error_code' => '0',
    'message' => 'All Quiz List.',
    'data' => $any_data
    );

    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');

    echo json_encode($output);
    die();

    }


	public function quizlist() {
            global $wpdb;
            date_default_timezone_set(LOCAL_TIMEZONE);
            $params = filter_input_array(INPUT_POST, $_POST);
            $params = $_REQUEST;
            $limit=100;
            $start=0;
            $user_id=$params['userid'];
            if(isset($params['start']) && $params['start'] !='')
            {
                $start=$params['start'];
            }

            if(isset($params['limit']) && $params['limit'] !='')
            {
                $limit=$params['limit'];
            }
            
            $sortby="id";
            if(isset($params['sortby']) && $params['sortby']!='')
            {
              $sortby=$params['sortby'];
              
            }
           $orderbyquery=" order by ".$sortby." desc" ;
           if(isset($params['orderby']) && $params['orderby']!='')
           {
               $orderby= $params['orderby'];
              $orderbyquery=" order by ".$sortby." ".$orderby;
           }
       
           $category_query='';
           if(isset($params['catid']) && $params['catid'] !='')
           {
              $catid=$params['catid'];
              $category_query =" && quiz_category_id='$catid'";
           }
           $subcategory_query='';
           if(isset($params['subid']) && $params['subid'] !='')
           {
              $subid=$params['subid'];
              $subcategory_query =" && quiz_subcategory_id='$subid'";
              
           }
             
            $current_time   = current_time( 'timestamp' );
            $sql= $wpdb->get_results("SELECT * from ".$wpdb->prefix."aysquiz_quizes where published='1' AND ((active_end_date > '$current_time' && quiz_type ='0' ) OR quiz_type ='1')".$category_query.$subcategory_query.$orderbyquery." limit ".$start.",".$limit, ARRAY_A);
            $data= array();
            $any_data = array();
            foreach($sql as $value)
            {         
                $total_question = explode(',',$value['question_ids']);
                $option = json_decode($value['options'],true);
                $id = $value['id'];

                $sql_result_table= $wpdb->get_results("SELECT quiz_id,user_id,current_timestamp from ".$wpdb->prefix."aysquiz_reports where quiz_id='$id' && user_id='$user_id'", ARRAY_A);
                if(count($sql_result_table) > 0)
                {
    
                    foreach($sql_result_table as $sql_result_table_hwe)
                    {
                        $quiz_curerent_timesxtamp = $sql_result_table_hwe['current_timestamp'];
                        if($quiz_curerent_timesxtamp > 0)
                        {
                            $data['disabled_enter_status'] = 1;
                        }
                        else
                        {
                            $data['disabled_enter_status'] = 0;
                        }
                    }
                    
                }
                else
                {
                    $data['disabled_enter_status'] = 0;
                }


                $sql1= $wpdb->get_results("SELECT * from ".$wpdb->prefix."purchase where quiz_id='".$id."'", ARRAY_A);
                
                $booked_slots = count($sql1);

                $sql1_hwe= $wpdb->get_results("SELECT * from ".$wpdb->prefix."purchase where user_id='".$user_id."' AND quiz_id='".$id."'", ARRAY_A);
                $purchase=0;
                if(count($sql1_hwe)>0)
                {
                    $purchase=1;
                }

                $current_time   = current_time( 'timestamp' );
                $activecreatedate= $value['active_create_date'];

                $activeenddate=$value['active_end_date'];

                if($current_time >= $activecreatedate && $current_time <= $activeenddate)
                {
                    $countdowntime=0;
                
                }
                else 
                {  
                    $countdowntime= $value['active_create_date'] - $current_time; 
                }
                
                $total_marks=0;

                foreach($total_question as $total_question_hwe)
                {
                    $get_questions_points= $wpdb->get_results("SELECT id,`weight` from ".$wpdb->prefix."aysquiz_questions where id='".$total_question_hwe."'",ARRAY_A);
                    foreach($get_questions_points as $get_questions_points_hwe)
                    {
                        $points =0;
                        $points = $get_questions_points_hwe['weight'];
                    }
                
                    $total_marks = $total_marks + $points;
                    
                }

                    $total_minutes = $activeenddate - $activecreatedate;

                    $total_slots = abs($value['total_slots']);
                    $booked = $booked_slots;
                    $remaining_slots = $value['total_slots'] - $booked_slots;
                    $booked_inper = ($booked *100)/$total_slots;
                    $remain_inper = ($remaining_slots * 100)/$total_slots;

                    $data['id']= $value['id'];
                    $data['title']= $value['title'];
                    $data['desc']= $value['description'];
                    $data['quiz_image']= $value['quiz_image'];
                    $data['total_ques']= count($total_question);
                    $data['total_min'] = $total_minutes;
                    $data['total_marks']= $total_marks;
                    $data['quiz_fee']= $value['price'];
                    $data['total_slots']= $value['total_slots'] ;
                    $data['booked_slots']= $booked_slots;
                    $data['available_slots']= $value['total_slots'] - $booked_slots;
                    $data['booked_slots_inper']= round($booked_inper,2).'%';
                    $data['available_slots_inper']= round($remain_inper,2).'%';
                    $data['start_date']=$option['activeInterval'];
                    $data['end_date']=$option['deactiveInterval'];
                    $data['test_start_in_timestamp']= $value['active_create_date'];
                    $data['test_end_in_timestamp']= $value['active_end_date'];
                    $data['quiz_countdown_time'] =$countdowntime;
                    $data['purchase']=$purchase;
                    $data['quiz_type']=(int)$value['quiz_type'];
                    $data['total_prize_money'] = $value['total_prize_money'];
                    $any_data[]=$data;
            }

            $output = array('status' => true, 'error_code' => '0',
            'message' => 'All Quiz List.',
            'data' => $any_data
            );
            echo json_encode($output);
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            die();

    }




    public function purchasequizlist() {
        // error_log("\n=================== Login ==================\n");
        //$this->app_version();

        global $wpdb;
        $params = filter_input_array(INPUT_POST, $_POST);

        $params = $_REQUEST;
            
        $limit=100;
        $start=0;
        if(isset($params['start']) && $params['start'] !='')
        {
            $start=$params['start'];
        }

        if(isset($params['limit']) && $params['limit'] !='')
        {
            $limit=$params['limit'];
        }

        $sortby="id";
        if(isset($params['sortby']) && $params['sortby']!='')
       {
          $sortby=$params['sortby'];
          
       }
       $orderbyquery=" order by ".$sortby." desc" ;
       if(isset($params['orderby']) && $params['orderby']!='')
       {
           $orderby= $params['orderby'];
          $orderbyquery=" order by ".$sortby." ".$orderby;
       }

       $user_id=$params['userid'];
      
   
       $category_query='';
       if(isset($params['catid']) && $params['catid'] !='')
       {
          $catid=$params['catid'];
        
          $category_query =" && quiz_category_id='$catid'";
       }
       $subcategory_query='';
       if(isset($params['subid']) && $params['subid'] !='')
       {
          $subid=$params['subid'];
          $subcategory_query =" && quiz_subcategory_id='$subid'";
       }

       $imploed_quiz_ids_hwe=0;
       $sql1_hwe= $wpdb->get_results("SELECT quiz_id from ".$wpdb->prefix."purchase where user_id='".$user_id."' order by id asc", ARRAY_A);
     
        $data= array();
        $any_data = array();
        $implode_quiz_id_query='';     
       if(count($sql1_hwe)>0)
       {
         $quiz_ids_hwe=array();
         foreach($sql1_hwe as $value_hwe)
         {

           $quiz_ids_hwe = $value_hwe['quiz_id'];
       
           $current_time   = current_time( 'timestamp' );

              $sql = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."aysquiz_quizes as a INNER JOIN ".$wpdb->prefix."purchase as b ON a.id=b.quiz_id where a.published='1' && (a.active_end_date > '$current_time') && a.id='$quiz_ids_hwe' && b.user_id='$user_id' order by a.id desc limit 0,100",ARRAY_A);
                if(count($sql) > 0)
                {
                    foreach($sql as $value)
                    {
                        $data =array();

                        $total_question = explode(',',$value['question_ids']);
                        $option = json_decode($value['options'],true);
                        $id = $value['quiz_id'];

                        $sql_result_table= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_reports where quiz_id='$id' && user_id='$user_id'", ARRAY_A);
                        if(count($sql_result_table) > 0)
                        {

                            foreach($sql_result_table as $sql_result_table_hwe)
                            {
                                $quiz_curerent_timesxtamp = $sql_result_table_hwe['current_timestamp'];
                                if($quiz_curerent_timesxtamp > 0)
                                {
                                    $data['disabled_enter_status'] = 1;
                                }
                                else
                                {
                                    $data['disabled_enter_status'] = 0;
                                }
                            }
                        
                        }
                        else
                        {
                            $data['disabled_enter_status'] = 0;
                        }


                        $sql1= $wpdb->get_results("select * from ".$wpdb->prefix."purchase where quiz_id='".$id."'", ARRAY_A);
                        
                        $booked_slots = count($sql1);
                     
                        $data['purchase']=$value['purchased_quiz'];
                        $activecreatedate= $value['active_create_date'];

                        if($current_time>=$activecreatedate && $current_time<= $activeenddate)
                        {
                            $countdowntime=0;
                            
                        }
                        else 
                        {  
                            $countdowntime= $value['active_create_date'] - $current_time; 
                        }


                        $total_marks=0;

                        foreach($total_question as $total_question_hwe)
                        {
                            $get_questions_points= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_questions where id='".$total_question_hwe."'", ARRAY_A);
                            foreach($get_questions_points as $get_questions_points_hwe)
                            {
                                $points =0;
                                $points = $get_questions_points_hwe['weight'];
                            }
                        
                            $total_marks = $total_marks + $points;
                            
                        }

                            $total_minutes = $activeenddate - $activecreatedate;

                    
                            $total_slots = abs($value['total_slots']);
                            $booked = $booked_slots;
                            $remaining_slots = $value['total_slots'] - $booked_slots;
                            $booked_inper = ($booked *100)/$total_slots;
                            $remain_inper = ($remaining_slots * 100)/$total_slots;

                            $data['id']= $value['quiz_id'];
                            $data['title']= $value['title'];
                            $data['desc']= $value['description'];
                            $data['quiz_image']= $value['quiz_image'];
                            $data['total_ques']= count($total_question);
                            $data['total_min']= $total_minutes;
                            $data['total_marks']= $total_marks;
                            // $data['total_prize_money']= $value['total_prize_money']; 
                            $data['quiz_fee']= $value['price'];
                            $data['total_slots']= $value['total_slots'] ;
                            $data['booked_slots']= $booked_slots;
                            $data['available_slots']= $value['total_slots'] - $booked_slots;

                            $data['booked_slots_inper']= round($booked_inper,2).'%';
                            $data['available_slots_inper']= round($remain_inper,2).'%';

                            $data['start_date']=$option['activeInterval'];
                            $data['end_date']=$option['deactiveInterval'];
                            $data['test_start_in_timestamp']= $value['active_create_date'];
                            $data['test_end_in_timestamp']=$value['active_end_date'];
                            $data['quiz_countdown_time'] =$countdowntime;
                            $data['quiz_type']=(int)$value['quiz_type'];
                            $data['total_prize_money'] = $value['total_prize_money'];
                            $any_data[]=$data;
                    }
                }

            }
        }

        //check if there are no errors

        $output = array('status' => true, 'error_code' => '0',
        'message' => 'All Quiz List.',
        'data' => $any_data
        );

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode($output);
        die();

    }


    public function user_test_start() {
        // error_log("\n=================== Login ==================\n");
         //$this->app_version();
         
         global $wpdb;
         //date_default_timezone_set(LOCAL_TIMEZONE);
         $creds = $output = [];
         $this->db = $this->getDb();
         $headers = $this->getallheaders();
         $params = filter_input_array(INPUT_POST, $_POST);
      
         $params = $_REQUEST;
    
         
         $user_id = $params['userid'];

         $quiz_id = $params['quizid'];
         $start_date_with_time1 = strtotime($params['start_date_with_time']);

         header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
         header('Content-type: application/json');
         header('Access-Control-Allow-Origin: *');

         if(!$user_id ||  !$quiz_id)
         {

            $output = array('status' => false, 'error_code' => '1109',
            'message' => 'Please login again.',
            'data' => 'Records not inserted successfully.'
            
            );

            echo json_encode($output);

            die();

         }
         else
         {
           // $start_date_with_time = date("Y-m-d H:i:s",$start_date_with_time1);

               
            $get_quiz_start_time_hwe= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quiz_id."'", ARRAY_A);
          
            $quiz_start_time_strtotime=0;
            foreach($get_quiz_start_time_hwe as $value_start_time_hwe)
           {
   
               $quiz_start_time = $value_start_time_hwe['active_create_date'];
              // $quiz_start_time_strtotime =   $value_start_time_hwe['active_create_date'];
   
           }
           
   
         
   
            $current_time_hwe =current_time('timestamp');
   
            $startdate= date("Y-m-d H:i:s",$current_time_hwe);
   
            //if($quiz_start_time_strtotime > $start_date_with_time1)
            //{
             
               $start_date_with_time = date("Y-m-d H:i:s",$quiz_start_time);
           // }
        
           //  $enddate= $params['end_date'];
           // $user_ip=$params['user_ip'];

           $aysquiz_reports_hwe= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_reports where quiz_id='".$quiz_id."' AND user_id='$user_id'", ARRAY_A);

           if(count($aysquiz_reports_hwe) <= 0)
           {
                $sql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."aysquiz_reports set `user_id`= '$user_id', `quiz_id`='$quiz_id', `start_date`='$start_date_with_time', `end_date`='', `user_ip`='', `user_email`='', `user_phone`='', `status`='started', `user_name`='' ");
    
                $wpdb->query($sql);
           }
           
     
           

           $output = array('status' => true, 'error_code' => '0',
           'message' => 'All Quiz List.',
           'data' => 'Records inserted successfully'
           
           );

           echo json_encode($output);
            die();
   
          
         }

         



         
            
                    

    }

    public function user_test_finish() {
        // error_log("\n=================== Login ==================\n");
         //$this->app_version();
         
         global $wpdb;
         //date_default_timezone_set(LOCAL_TIMEZONE);
         $creds = $output = [];
         $this->db = $this->getDb();
         $headers = $this->getallheaders();
         $params = filter_input_array(INPUT_POST, $_POST);
      
         $params = $_REQUEST;
    
         $user_id = $params['userid'];
         $quiz_id = $params['quizid'];
    
         $end_date_with_time1 = strtotime($params['end_date_with_time']);
         $end_date_with_time = date("Y-m-d H:i:s",$end_date_with_time1);

       // update_option("end_date_with_time",$end_date_with_time);

       $get_quiz_start_time_hwe= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quiz_id."'", ARRAY_A);
        
       $quiz_end_time_strtotime=0;
        foreach($get_quiz_start_time_hwe as $value_start_time_hwe)
        {

            $quiz_end_time = $value_start_time_hwe['active_end_date'];
            $quiz_end_time_strtotime =  $value_start_time_hwe['active_end_date'];

        }

        if($quiz_end_time_strtotime  < $end_date_with_time1)
        {
            $end_date_with_time = date("Y-m-d H:i:s",$quiz_end_time_strtotime);
        }

         $current_time_hwe =current_time('timestamp');

         $enddate= date("Y-m-d H:i:s",$current_time_hwe);
        //  $enddate= $params['end_date'];
        // $user_ip=$params['user_ip'];
  
        $sql = $wpdb->prepare("UPDATE ".$wpdb->prefix."aysquiz_reports set end_date='$end_date_with_time' where quiz_id='$quiz_id' && user_id='$user_id'");

        $wpdb->query($sql);



                    $output = array('status' => true, 'error_code' => '0',
                    'message' => 'All Quiz List.',
                    'data' => 'Records Updated Successfully successfully'
                    
                    );
            
                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
            
                    echo json_encode($output);
                    die();

    }

               
    public function questions_answers() {
        // error_log("\n=================== Login ==================\n");
        //$this->app_version();
      


        global $wpdb;
       // date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $params = $_REQUEST;
            
        $userid=$params['userid'];
        $quizid=$params['quizid'];
        

        // if($quiz_start_time == '')
        // {
        //     $output = array('status' => false, 'error_code' => '1109',
        //             'message' => 'Quiz start time empty.',
                    
        //             );
            
        //         header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        //         header('Content-type: application/json');
        //         header('Access-Control-Allow-Origin: *');
        
        //         echo json_encode($output);
        //         die();
        // }

        $question_ans =array();
        $any_data2=array();
       

        $sql= $wpdb->get_results("select * from usertestlang where userid='".$userid."' and quizid='".$quizid."' ", ARRAY_A);

            
            foreach($sql as $value)
        {
           $testlang=$value["testlang"];
    
        }
         
        $sql2= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quizid."'", ARRAY_A);
           
          foreach($sql2 as $value2)

          {
                $questionid=$value2["question_ids"];

                $current_time   = current_time( 'timestamp' );
                $activecreatedate= $value2['active_create_date'];

                $activeenddate=$value2['active_end_date'];

                
                if($current_time >= $activecreatedate && $current_time <= $activeenddate)
                {
                    $countdowntime=0;
                    
                }
                else 
                {  
                        $countdowntime= $value2['active_create_date'] - $current_time; 
                }

                $remain_time = $activeenddate - $activecreatedate;

                $cureent_time_remain = $value2['active_end_date'] - $current_time;



                // $quiz_start_time_timestamp = strtotime($current_time);

                if($cureent_time_remain > $remain_time)
                {
                    $quiz_time=$remain_time;
                }
                else
                {
                    $quiz_time=$cureent_time_remain;

                }
                
                
                

                // $question_ans['quiz_time']=$quiz_time;


                // $question_ans['countdowntime']= $countdowntime;
                $test_start_in_timestamp= $value2['active_create_date'];
                $test_end_in_timestamp= $value2['active_end_date'];

                $quiz_name= $value2['title'];

                if($value2['quiz_type'] == '1')
                {
                    $quiz_time = 300;
                }
             
           
          }



          $sql3= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_questions where id IN(".$questionid.") order by id desc", ARRAY_A);
         
          
          
          

            foreach($sql3 as $value3)
  
            {         

                if($testlang=='1')
                {
                    $question_ans['question']=$value3["question_hindi"];
                    $question_ans['question_id']=$value3["id"];
                }
                else
                {
                  $question_ans['question']=$value3["question"];
                  $question_ans['question_id']=$value3["id"];
                }

                $questionid = $value3["id"];

                // if($value3["question_image"] !='')
                // { 
                    
                    $question_ans['question_image']=$value3["question_image"];
                    

                //}
                
                $question_ans4 =array();
                $answers_array =array();
                $answer_data_all=array();
                $answer_data_all2=array();
                $answer_data_all3=array();
                $sql4= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_answers where question_id ='".$questionid."'", ARRAY_A);
                
                foreach($sql4 as $key => $value4)
                {
                    $answerid=$value4["id"];
                    //$answers_array = array();
                    unset($answers_array);
                    if($testlang=='1')
                    {
                        //$answers_array['answer']=$value4["answer"];
                       // $answers_array['answer_id']=$value4["id"];
                       $answer_data_all[] = array(
                            "answer"=>$value4["answer_hindi"]
                       );
                       $answer_data_all2[]=array(
                        "answerid"=>$answerid
                   );
                   $answer_data_all3[]=array(
                    "answerimage"=>$value4["image"]
               );
                        
                    }
                    else 
                    {
                        //$answers_array['answer']=$value4["answer"];
                       // $answers_array['answer_id']=$value4["id"];
                       $answer_data_all[] = array(
                                "answer"=> $value4["answer"]
                        );
                        $answer_data_all2[]=array(
                            "answerid"=>$answerid
                       );
                       $answer_data_all3[]=array(
                        "answerimage"=>$value4["image"]
                   );
                    }

                    if($value4["image"] !='')
                    {
                    //     $answers_array['answer_image']=$value4["image"];
                    //     $answers_array = array(
                    //         'answer_image'=>=$value4["answer"]
                    //    );
                    
                    }

                    //=(object) $answers_array;
            
                }
                
             
                $question_ans["answers"] = $answer_data_all;
                $question_ans["answersid"] = $answer_data_all2;
                $question_ans["answersimage"] = $answer_data_all3;
                
             
                

                $any_data2[] = $question_ans;

             
               
                   
            }


   
            $output='';
                  
                if(count($any_data2)>0)
                {
                    $output = array('status' => true, 'error_code' => '0',
                    'message' => 'Quiz Question and Answers list.',
                    'data' => $any_data2,
                    'quiz_time'=>$quiz_time,
                    'quiz_name'=>$quiz_name,
                    'countdowntime'=>$countdowntime,
                    'test_start_in_timestamp'=>$test_start_in_timestamp,
                    'test_end_in_timestamp'=>$test_end_in_timestamp
                 
                    
                    );

                }
                else
                {
                    $output = array('status' => true, 'error_code' => '1154',
                    'message' => 'Quiz Question and Answers list Not Found.',
                    'data' => $any_data2
                    
                    );
                } 
                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
        
                echo json_encode($output);
                die();

}

public function test_remain_time() {
    // error_log("\n=================== Login ==================\n");
    //$this->app_version();

    global $wpdb;
    date_default_timezone_set(LOCAL_TIMEZONE);
    $creds = $output = [];
    $this->db = $this->getDb();
    $headers = $this->getallheaders();
    $params = filter_input_array(INPUT_POST, $_POST);

    $params = $_REQUEST;

    $quizid=$params['quizid'];

    $sql2= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quizid."'", ARRAY_A);
       
    $countdowntime='';
    foreach($sql2 as $value2)

    {
        $current_time   = current_time( 'timestamp' );
        $activecreatedate= $value2['active_create_date'];

        $countdowntime= $activecreatedate - $current_time; 

        if($value2['quiz_type'] == 1)
        {
            $countdowntime = 10;
        }
    
    }


    $output = array('status' => true, 'error_code' => '0',
    'message' => 'Test Remain Time.',
    'countdown' => $countdowntime
    
    
    );
    
    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');

    echo json_encode($output);
    die();

}   
public function terms_condition() {
    // error_log("\n=================== Login ==================\n");
    //$this->app_version();

    global $wpdb;
    date_default_timezone_set(LOCAL_TIMEZONE);
    $creds = $output = [];
    $this->db = $this->getDb();
    $headers = $this->getallheaders();
    $params = filter_input_array(INPUT_POST, $_POST);

    $params = $_REQUEST;

    $quizid=$params['quizid'];

    $sql2= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quizid."'", ARRAY_A);
       
    $age_state_validation='';
    $price_agreement='';
    $explode_terms_condition =array();
    foreach($sql2 as $value2)

    {
        $age_state_validation   = $value2['age_state_validation'];
        $price_agreement   = $value2['price_agreement'];

        $multiple_terms_condition   = $value2['multiple_terms_condition'];

        $explode_terms_condition = explode(',',$multiple_terms_condition);


    
    }

    $data_hwe =array();

    if(count($explode_terms_condition) > 0)
    {
        $items = array();

        $items_hwe1 =array();
        $items_hwe2=array();
        if($age_state_validation !='')
        {
            $items_hwe1['id']= 0;
            $items_hwe1['key']= $age_state_validation;
            $items_hwe1['checked']= false;

            $data_hwe[0]=$items_hwe1;
        }
        

       

        if($price_agreement != '')
        {
            $items_hwe2['id']= 1;
            $items_hwe2['key']= $price_agreement;
            $items_hwe2['checked']= false;

            $data_hwe[1]=$items_hwe2;
        }
    

        

         $key = 2;
         foreach($explode_terms_condition as $explode_terms_condition_hwe)
         {
            if($explode_terms_condition_hwe == '')
            {
                continue;
            }
            $items['id']= $key;
            $items['key']= $explode_terms_condition_hwe;
            $items['checked']= false;

            $data_hwe[$key] = $items;

            $key++;
         }
    }
    else
    {
        
       
        $items_hwe1 =array();
        $items_hwe2=array();

        if($age_state_validation !='')
        {
            $items_hwe1['id']= 0;
            $items_hwe1['key']= $age_state_validation;
            $items_hwe1['checked']= false;
    
            $data_hwe[0]=$items_hwe1;
        }
        

        if($price_agreement != '')
        {
            $items_hwe2['id']= 1;
            $items_hwe2['key']= $price_agreement;
            $items_hwe2['checked']= false;
    
            $data_hwe[1]=$items_hwe2;
        }
        
    }
    



    $output = array('status' => true, 'error_code' => '0',
    'message' => 'Terms List.',
    'age_state_validation' => $age_state_validation,
    'price_agreement' => $price_agreement,
    'multiple_terms_conditions' => $data_hwe
    
    
    );
    
    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');

    echo json_encode($output);
    die();

}   

public function citycountries() {
    // error_log("\n=================== Login ==================\n");
    //$this->app_version();

    global $wpdb;
    date_default_timezone_set(LOCAL_TIMEZONE);
    $creds = $output = [];
    $this->db = $this->getDb();
    $headers = $this->getallheaders();
    $params = filter_input_array(INPUT_POST, $_POST);

    $params = $_REQUEST;

    $sql= $wpdb->get_results("select * from ".$wpdb->prefix."options where option_name='um_fields'", ARRAY_A);

    $data= array();
    $any_data = array();
    foreach($sql as $value)
   {
        $data['id']= $value['option_id'];
        $state= unserialize($value['option_value']);
        
        $stateoption=$state['State']; 
        
         
        $any_data['state']=$stateoption['options'];
   }

        $any_data['country'] ='India';

         
          //check if there are no errors
       

            $output = array('status' => true, 'error_code' => '0',
            'message' => 'Category List.',
            'data' => $any_data
          
            
            );
    
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
    
            echo json_encode($output);
            die();

    }       


    public function unlock_quiz_test() {
                // error_log("\n=================== Login ==================\n");
                //$this->app_version();
          
                global $wpdb;
                date_default_timezone_set(LOCAL_TIMEZONE);
                $creds = $output = [];
                $this->db = $this->getDb();
                $headers = $this->getallheaders();
                $params = filter_input_array(INPUT_POST, $_POST);

                $params = $_REQUEST;
 
                $user_id=$params['user_id'];
                $quizID=$params['quiz_id'];
                // $metaID=$params['meta_id'];
                
                // $id = $params['id'];

                $sql1= $wpdb->get_results("select * from ".$wpdb->prefix."purchase where quiz_id='".$quizID."'", ARRAY_A);
                   
                $booked_slots = count($sql1);
        
                $sql= $wpdb->get_results("select * from ".$wpdb->prefix."aysquiz_quizes where id='".$quizID."'", ARRAY_A);
               
               
                $data= array();
                $money_rules_array = array();
                foreach($sql as $value)
               {

                    $data['total_slots']= $value['total_slots'] ;
                    $data['booked_slots']= $booked_slots;
                    $data['available_slots']= $value['total_slots'] - $booked_slots;


                    $quiz_price=0;
                    $rank_1_multiplier=0;
                    $rank_2_multiplier=0;
                    $rank_3_multiplier=0;
                    $rank_4_multiplier=0;

                    $quiz_price= $value['price'];
                    $rank_1_multiplier = $value['rank_1_multiplier'];
                    $rank_2_multiplier = $value['rank_2_multiplier'];
                    $rank_3_multiplier = $value['rank_3_multiplier'];
                    $rank_4_multiplier = $value['rank_4_multiplier'];

                    $data['id']= $value['id'];
                    $data['title']= $value['title'];
                    $data['desc']= $value['description'];
                    $data['quiz_price']= (int)$quiz_price;
                    //$data['testlang']=$value['language'];
                    $total_prize_money = 0;
                    $total_prize_money1 =0;
                    $total_prize_money2=0;
                    $total_prize_money3=0;
                    $total_prize_money4=0;
                    $total_prize_money5=0;
                    if(!empty($value['rank_1']))
                    {
                        $all_rules_array1['rank'] ='1';
                        $all_rules_array1['prize_money'] =$quiz_price * $rank_1_multiplier;
                        $all_rules_array1['no_of_winners'] =$value['rank_1'];
                        $money_rules_array[0]= $all_rules_array1;

                        $total_prize_money1 = $total_prize_money1 + (($quiz_price * $rank_1_multiplier) * $value['rank_1']);
    
                    }

                    if(!empty($value['rank_2']))
                    {
                        $all_rules_array2['rank'] =2;
                        $all_rules_array2['prize_money'] =$quiz_price * $rank_2_multiplier;
                        $all_rules_array2['no_of_winners'] =$value['rank_2'];
                        $money_rules_array[1]= $all_rules_array2;

                        $total_prize_money2 = $total_prize_money2 + (($quiz_price * $rank_2_multiplier) * $value['rank_2']);
    
                    }

                    if(!empty($value['rank_3']))
                    {
                        $all_rules_array3['rank'] =3;
                        $all_rules_array3['prize_money'] =$quiz_price * $rank_3_multiplier;
                        $all_rules_array3['no_of_winners'] =$value['rank_3'];
                        $money_rules_array[2]= $all_rules_array3;

                        $total_prize_money3 = $total_prize_money3 + (($quiz_price * $rank_3_multiplier) * $value['rank_3']);
    
                    }

                    if(!empty($value['rank_4']))
                    {
                        $all_rules_array4['rank'] =4;
                        $all_rules_array4['prize_money'] =$quiz_price * $rank_4_multiplier;
                        $all_rules_array4['no_of_winners'] =$value['rank_4'];
                        $money_rules_array[3]= $all_rules_array4; 

                        $total_prize_money4 = $total_prize_money4 + (($quiz_price * $rank_4_multiplier) * $value['rank_4']);
    
                    }
                  
                   
                    if(!empty($value['rank_5']))
                    {

                        $multiple_rank = explode(',',$value['rank_5']);
                        $multiple_rank_multiplier= explode(',', $value['rank_5_multiplier']);

                        $counth=5;
                        $countkey=4;
                        foreach($multiple_rank as $key => $value_hwe3)
                        {
                        

                            $all_rules_array['rank'] =$counth;
                            $all_rules_array['prize_money'] =$quiz_price * $multiple_rank_multiplier[$key];
                            $all_rules_array['no_of_winners'] =$value_hwe3;
                            $money_rules_array[$countkey]= $all_rules_array;

                            $total_prize_money5 = $total_prize_money5 + (($quiz_price * $multiple_rank_multiplier[$key]) * $value_hwe3);

                            $counth++;
                            $countkey++;
                        }
                    }

                    
               }

               $total_prize_money = $total_prize_money1 + $total_prize_money2 + $total_prize_money3 + $total_prize_money4 +$total_prize_money5;
                  
               $data['total_prize_money'] = $total_prize_money;
             
               $bonuus_wallet_price=0;
                $wallet_price=0;
                $admin_bonuus_wallet_price=0;

                $wallet_price = get_user_meta($user_id,'mwb_wallet',true);
                if(empty($wallet_price))
                {
                    $wallet_price=0;
                   
                }
                else
                {
                    $wallet_price= get_user_meta($user_id,'mwb_wallet',true);
                }

                $bonuus_wallet_price = get_user_meta($user_id,'bonuus_wallet',true);
                if(empty($bonuus_wallet_price))
                {
                    $bonuus_wallet_price=0;
                   
                }
                else
                {
                    $bonuus_wallet_price= get_user_meta($user_id,'bonuus_wallet',true);
                }

                $admin_bonuus_wallet_price = get_user_meta($user_id,'admin_bonuus_amount',true);
                if(empty($wallet_price))
                {
                    $admin_bonuus_wallet_price=0;
                   
                }
                else
                {
                    $admin_bonuus_wallet_price= get_user_meta($user_id,'admin_bonuus_amount',true);
                }
               
                $data['wallet_price']= abs($wallet_price);
                $data['bonuus_wallet_price']= abs($bonuus_wallet_price);
                $data['admin_bonuus_wallet_price']= abs($admin_bonuus_wallet_price);

           
     
                $output = array('status' => true, 'error_code' => '0',
                'message' => 'Terms and Condition',
                'data' => $data,
                
                'prize_rules' =>$money_rules_array

                );
        
                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
        
                echo json_encode($output);
                die();
    
    }

    public function Test_language() {
        // error_log("\n=================== Login ==================\n");
        //$this->app_version();
  
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $params = $_REQUEST;

        $userid = $params['userid'];
        $quizid = $params['quizid'];
        $testlang = $params['testlang'];

        if($params['userid'] == '' || $params['quizid'] == '')
        {
            $output = array('status' => false, 'error_code' => '1109',
            'message' => "Please Again Login");
        
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        $sql8 = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE published='1' and id ='".$quizid."'";
        $result8 = $wpdb->get_results( $sql8, ARRAY_A );
        foreach ($result8 as $quiz_id_data)
        {
            $quiz_price=$quiz_id_data['price'];
            $quiz_price = abs($quiz_price);	
            $title=$quiz_id_data['title'];	
            
        } 
        
        
        
        $bonuus_wallet_bal     = abs(get_user_meta( $userid, 'bonuus_wallet', true ));
        $withdraw_wallet_bal     = abs(get_user_meta( $userid, 'mwb_wallet', true ));
        $admin_bonuus_wallet_bal     = abs(get_user_meta( $userid, 'admin_bonuus_amount', true ));

        $total_wallet_price = abs($bonuus_wallet_bal) + abs($withdraw_wallet_bal) + abs($admin_bonuus_wallet_bal);
        
        $total_admin_user_bonuus_amount = $admin_bonuus_wallet_bal + $bonuus_wallet_bal;

        if($total_wallet_price >= $quiz_price)
        {
           
                
       
                $sql_purchase = "SELECT * FROM {$wpdb->prefix}purchase WHERE quiz_id='".$quizid."' && user_id='$userid'";
        
                $result_purchase = $wpdb->get_results( $sql_purchase, ARRAY_A );
                if(count($result_purchase) > 0)
                {

    
                    $output = array('status' => false, 'error_code' => '1109',
                    'message' => "Test Already Purchased");

                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
        
                    print json_encode($output);
                    die();
                }
                else
                {
                    $purchase_quiz = "INSERT INTO ".$wpdb->prefix."purchase SET user_id='$userid',
                    quiz_id='$quizid',pruchase_value='1',purchased_quiz='1',created=now()";
                    $wpdb->query($purchase_quiz);

                    if($total_admin_user_bonuus_amount >=  $quiz_price)
                    {
                        if($admin_bonuus_wallet_bal >= $quiz_price)
                        {
                            $remain_admin_balance = $admin_bonuus_wallet_bal - $quiz_price;

                            update_user_meta( $userid, 'admin_bonuus_amount', abs( $remain_admin_balance ) );
                        }
                        else
                        {
                            $remain_quiz_price = $quiz_price - $admin_bonuus_wallet_bal;
                            $remain_balance = $bonuus_wallet_bal - $remain_quiz_price;

                            update_user_meta( $userid, 'bonuus_wallet', abs( $remain_balance ) );
                            update_user_meta( $userid, 'admin_bonuus_amount', abs(0) );
                        }
                    }
                    else
                    {

                        $remain_quiz_price = $quiz_price - ($bonuus_wallet_bal +$admin_bonuus_wallet_bal);
                        $remain_balance = $withdraw_wallet_bal - $remain_quiz_price;

                        update_user_meta( $userid, 'bonuus_wallet', abs(0) );
                        update_user_meta( $userid, 'admin_bonuus_amount', abs(0) );
                        update_user_meta( $userid, 'mwb_wallet', abs( $remain_balance ) );
                        
                    }

                    $insert_transaction_data= $wpdb->prepare(
                        "INSERT INTO ".$wpdb->prefix."mwb_wsfw_wallet_transaction SET `user_id`='$userid',
                                                    `amount`='$quiz_price',
                                                    `currency`='INR',
                                                    `transaction_type`='Amount deducted for Quiz".$title."',
                                                    `payment_method`='Manually By Admin',
                                                    date =now()");
                    
                    $wpdb->query($insert_transaction_data);

                    $insert_query_hwe1 = "INSERT INTO usertestlang (userid, quizid, testlang) values ('$userid', '$quizid','$testlang')";
                    $wpdb->query($insert_query_hwe1);
                            
                    $output = array('status' => true, 'error_code' => '0',
                    'message' => "Test Purchased Successfully");
                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
        
                    print json_encode($output);
                    die();
                }
            
          
        }
        else
        {
            $output = array('status' => false, 'error_code' => '1109',
            'message' => "Insufficient balance");
        
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
         
    }

    public static function ays_get_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";

        if($wpdb->get_var("SHOW TABLES LIKE '$settings_table'") != $settings_table) {
            return false;
        }

        $sql = "SELECT meta_value FROM ".$settings_table." WHERE meta_key = '".$meta_key."'";
        $result = $wpdb->get_var($sql);
        if($result != ""){
            return $result;
        }
        return false;
    }


    protected function add_results_to_db($data){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';

        $started_status = isset($data['started_status']) ? $data['started_status'] : null;

        $user_ip = $data['user_ip'];
        $user_id = $data['user_id'];
        $user_name = $data['user_name'];
        $user_email = $data['user_email'];
        $user_phone = $data['user_phone'];
        $quiz_id = $data['quiz_id'];
        $start_date = $data['start_date'];
    //mohit


        $end_date = $data['end_date'];

    

        $score = $data['score'];
        $options = $data['answered'];
        $calc_method = $data['calc_method'];
        $options['passed_time'] = Quiz_Maker_Data::get_time_difference($start_date, $end_date);
        $options['user_points'] = $data['user_points'];
        $options['max_points'] = $data['max_points'];

        $duration = strtotime($end_date) - strtotime($start_date);

        
        $quiz_end_date_timestamp = $data['quiz_end_date_timestamp'];
        if($duration > $quiz_end_date_timestamp)
        {
            $duration = $quiz_end_date_timestamp;
        
        }

        $user_points = $data['user_points'];
        $max_points = $data['max_points'];
        $user_corrects_count = $data['user_corrects_count'];
        $questions_count = $data['questions_count'];
        
        $user_explanation = (count($data['user_explanation']) == 0) ?  '' : json_encode($data['user_explanation']);
        
        $cert_unique_code = isset($data['unique_code']) ? $data['unique_code'] : '';

        $cert_file_name = isset($data['cert_file_name']) ? $data['cert_file_name'] : '';
        $cert_file_path = isset($data['cert_file_path']) ? $data['cert_file_path'] : '';
        $cert_file_url = isset($data['cert_file_url']) ? $data['cert_file_url'] : '';

        $quiz_attributes_information = array();
        $quiz_attributes = Quiz_Maker_Data::get_quiz_attributes_by_id($quiz_id);

        foreach ($quiz_attributes as $attribute) {
            $quiz_attributes_information[strval($attribute->name)] = (isset($_REQUEST[strval($attribute->slug)])) ? $_REQUEST[strval($attribute->slug)] : '';
        }
        $options['attributes_information'] = $quiz_attributes_information;
        $options['calc_method'] = $calc_method;
        $options['cert_file_name'] = $cert_file_name;
        $options['cert_file_path'] = $cert_file_path;
        $options['cert_file_url'] = $cert_file_url;
        $options['answers_keyword_counts'] = isset($data['mv_keywords_counts']) && !empty($data['mv_keywords_counts']) ? $data['mv_keywords_counts'] : array();
        $options['quiz_coupon'] = (isset($data['quiz_coupon']) && $data['quiz_coupon'] != '') ? $data['quiz_coupon'] : '';

        if( isset($data['chained_quiz_id']) && $data['chained_quiz_id'] !== null){
            $options['chained_quiz_id'] = $data['chained_quiz_id'];
        }

        $db_fields = array(
            'quiz_id' => absint(intval($quiz_id)),
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'user_phone' => $user_phone,
            'user_ip' => $user_ip,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'score' => $score,
            'duration' => $duration,
            'points' => $user_points,
            'max_points' => $max_points,
            'corrects_count' => $user_corrects_count,
            'questions_count' => $questions_count,
            'user_explanation' => $user_explanation,
            'unique_code' => $cert_unique_code,
            'options' => json_encode($options),
            'current_timestamp'=>$duration,
            'status' => 'finished',
        );

        $db_fields_types = array(
            '%d', // quiz_id
            '%d', // user_id
            '%s', // user_name
            '%s', // user_email
            '%s', // user_phone
            '%s', // user_ip
            '%s', // start_date
            '%s', // end_date
            '%s', // score
            '%s', // duration
            '%s', // user_points
            '%s', // max_points
            '%s', // user_corrects_count
            '%s', // questions_count
            '%s', // user_explanation
            '%s', // unique_code
            '%s', // options
            '%s', // current_timestamp
            '%s', // status
        );

        if(is_null($started_status)){
            $results = $wpdb->insert(
                $results_table,
                $db_fields,
                $db_fields_types
            );
        }else{
            $results = $wpdb->update(
                $results_table,
                $db_fields,
                array( 'id' => absint(intval($started_status)) ),
                $db_fields_types,
                array( '%d' )
            );
        }

        if ($results >= 0) {
            return true;
        }

        return false;
    }


    public function ays_finish_quiz(){

        //  ini_set('display_errors', 1);
        //  ini_set('display_startup_errors', 1);
        //  error_reporting(E_ALL);

        global $wpdb;
    // date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        
        $params =$_REQUEST;
    

        if((!isset($_REQUEST['user_id']) || !$_REQUEST['user_id']) || (!isset($_REQUEST['end_date_with_time']) || !$_REQUEST['end_date_with_time'] || ($_REQUEST['end_date_with_time'] == '0000-00-00 00:00:00') ))
        {

            $output = array('status' => false, 'error_code' => '1109',
            'message' => 'Result Not submitted succesfully'
            );

            echo json_encode($output);

            die();

        }
        else
        {


        $questionoption= $_REQUEST['questionlist'];
        
        $newquestionoption= explode(",",$questionoption);
        $neww=array_filter($newquestionoption);
        $ques= array_values($neww);
        $arrayq=array();

        foreach ($ques as $value)
        {
        $quesansexplode= explode("hwe",$value);
        
        $quessexplode='question-id-'.$quesansexplode[0];
        if(array_key_exists($quessexplode,$arrayq))
        {
            unset($arrayq[$quessexplode]);
        
            $arrayq[$quessexplode]=$quesansexplode[1];
        }
        else
        {
            $arrayq[$quessexplode]=$quesansexplode[1];
        }
        
        
    
        }   

    
        $_REQUEST['ays_questions']=$arrayq;
        

    $quiz_id_hwe = $_REQUEST['quiz_id'];
    $user_id_hwe = $_REQUEST['user_id'];
    $end_date_with_time = $_REQUEST['end_date_with_time'];

    //update_option("end_date_with_time2",$end_date_with_time);

    $ays_answer_correct = array();
    
    $select = "SELECT * from ".$wpdb->prefix."aysquiz_reports where quiz_id='$quiz_id_hwe' && user_id='$user_id_hwe'";
    $result_hwe = $wpdb->get_results($select,ARRAY_A);
    $quiz_start_date='';
    if(count($result_hwe) > 0)
    {
        foreach($result_hwe as $result_hwe_hwe)
        {
            
            $_REQUEST['ays_quiz_result_row_id'] = $result_hwe_hwe['id'];
            $quiz_start_date = $result_hwe_hwe['start_date'];
            $strtime_start_date =strtotime($quiz_start_date);

            $start_date_format = date("Y-m-d H:i:s",$strtime_start_date);


        
            
            
            if($result_hwe_hwe['end_date'] != ''  && $result_hwe_hwe['end_date'] != '0000-00-00 00:00:00')
            {
                $current_time_hwe = strtotime($result_hwe_hwe['end_date']);
            
        
            }
            else
            {
            //$current_time_hwe =current_time('timestamp');
            // $end_date_format = date("Y-m-d H:i:s",$current_time_hwe);
                $current_time_hwe=strtotime($end_date_with_time);
            
            }

            


            
        }
        
    }

    $_REQUEST['start_date'] =$start_date_format;
    
    //$_REQUEST['end_date'] = "2022-09-01+14:25:00";
    


    
    $_REQUEST['ays_quiz_id']=$quiz_id_hwe;

    $quiz_end_date_time=0;
    $select1 = "SELECT * from ".$wpdb->prefix."aysquiz_quizes where id='$quiz_id_hwe'";

    $result_hwe1 = $wpdb->get_results($select1,ARRAY_A);
    if(count($result_hwe1) > 0)
    {
        foreach($result_hwe1 as $result_hwe_hwe1)
        {
            $question_idss = $result_hwe_hwe1['question_ids'];

            $quiz_end_date_time = $result_hwe_hwe1['active_end_date'];

            $active_create_date_start_time = $result_hwe_hwe1['active_create_date'];

            $quiz_total_time_for_user = $quiz_end_date_time - $active_create_date_start_time;
            
        }
        
    }

    if($current_time_hwe > $quiz_end_date_time)
    {
        $current_time_hwe = $quiz_end_date_time;
    }

    if($current_time_hwe < $active_create_date_start_time)
    {
        $current_time_hwe = $quiz_end_date_time;
    }


    $end_date_format = date("Y-m-d H:i:s",$current_time_hwe);

    
    $_REQUEST['end_date'] = $end_date_format;
    

    $_REQUEST['ays_quiz_questions']=$question_idss;



    $_REQUEST['ays_finish_quiz_nonce_'.$quiz_id_hwe] = 'dfgdfgdfg';
    $_REQUEST['action'] = 'ays_finish_quiz';
    $_REQUEST['ays_user_name'] = '';
    $_REQUEST['ays_user_email'] = '';
    $_REQUEST['ays_user_phone'] = '';
    $_REQUEST['ays_answer_correct'] = $ays_answer_correct;
    



        error_reporting(0);
        $quiz_id = isset($_REQUEST['ays_quiz_id']) ? absint(intval($_REQUEST['ays_quiz_id'])) : 0;
        if($quiz_id === 0){            
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array("status" => false, "message" => "No no no" ));
            die();
        } else {
            


            $limited_result_id = (isset($_REQUEST['ays_quiz_result_row_id']) && $_REQUEST['ays_quiz_result_row_id'] != '') ? absint(intval( $_REQUEST['ays_quiz_result_row_id'] )) : null;
            
            
            $questions_answers = (isset($_REQUEST["ays_questions"])) ? $_REQUEST['ays_questions'] : array();
        
        
            $questions_ids = preg_split('/,/', $_REQUEST['ays_quiz_questions']);
            
            
            
            
            $questions_answers = Quiz_Maker_Data::sort_array_keys_by_array($questions_answers, $questions_ids);
        
            $chained_quiz_id = (isset($_REQUEST['ays_chained_quiz_id']) && $_REQUEST['ays_chained_quiz_id'] != '') ? absint(intval( $_REQUEST['ays_chained_quiz_id'] )) : null;
            
            $chained_quiz_see_result = (isset($_REQUEST['ays_chained_quiz_see_result']) && $_REQUEST['ays_chained_quiz_see_result'] == 'on') ? true : false;
            
            $quiz = Quiz_Maker_Data::get_quiz_by_id($quiz_id);
        
            $quiz_intervals = json_decode($quiz['intervals']);
            
            $quiz_attributes = Quiz_Maker_Data::get_quiz_attributes_by_id($quiz_id);
            $options = json_decode($quiz['options']);
            if( is_array( $options ) ){
                $options = (object) $options;
            }

            $quiz_image = "";
            if( isset($quiz['quiz_image']) && $quiz['quiz_image'] != "" ){
                $quiz_image = $quiz['quiz_image'];
            }elseif( isset($options->quiz_bg_image) && $options->quiz_bg_image != "" ){
                $quiz_image = $options->quiz_bg_image;
            }

            $quiz_questions_count = Quiz_Maker_Data::get_quiz_questions_count($quiz_id);

            //if (isset($options->enable_question_bank) && $options->enable_question_bank == "on" && isset($options->questions_count) && intval($options->questions_count) > 0 && count($quiz_questions_count) > intval($options->questions_count)) {
                $question_ids = preg_split('/,/', $_REQUEST['ays_quiz_questions']);
            //} else {
            //    $question_ids = Quiz_Maker_Data::get_quiz_questions_count($quiz_id);
            //}
            // Strong calculation of checkbox answers
            $options->checkbox_score_by = ! isset($options->checkbox_score_by) ? 'on' : $options->checkbox_score_by;
            $strong_count_checkbox = (isset($options->checkbox_score_by) && $options->checkbox_score_by == "on") ? true : false;
            
            // Calculate the score
            $options->calculate_score = ! isset($options->calculate_score) ? 'by_correctness' : $options->calculate_score;
            $calculate_score = (isset($options->calculate_score) && $options->calculate_score != "") ? $options->calculate_score : 'by_correctness';

            // Disable store data 
            $options->disable_store_data = ! isset( $options->disable_store_data ) ? 'off' : $options->disable_store_data;
            $disable_store_data = (isset($options->disable_store_data) && $options->disable_store_data == 'off') ? true : false;

            // Display score option
            $display_score = (isset($options->display_score) && $options->display_score != "") ? $options->display_score : 'by_percentage';

            // Send interval message to user
            $options->send_interval_msg = ! isset( $options->send_interval_msg ) ? 'off' : $options->send_interval_msg;
            $send_interval_msg = (isset($options->send_interval_msg) && $options->send_interval_msg == 'on') ? true : false;
            
            // Send interval message to user
            $options->send_results_user = ! isset( $options->send_results_user ) ? 'off' : $options->send_results_user;
            $send_results_user = (isset($options->send_results_user) && $options->send_results_user == 'on') ? true : false;

            // Send interval message to admin
            $options->send_interval_msg_to_admin = ! isset( $options->send_interval_msg_to_admin ) ? 'off' : $options->send_interval_msg_to_admin;
            $send_interval_msg_to_admin = (isset($options->send_interval_msg_to_admin) && $options->send_interval_msg_to_admin == 'on') ? true : false;

            // Send interval message to admin
            $options->send_results_admin = ! isset( $options->send_results_admin ) ? 'on' : $options->send_results_admin;
            $send_results_admin = (isset($options->send_results_admin) && $options->send_results_admin == 'on') ? true : false;

            // Show interval message
            $options->show_interval_message = isset($options->show_interval_message) ? $options->show_interval_message : 'on';
            $show_interval_message = (isset($options->show_interval_message) && $options->show_interval_message == 'on') ? true : false;

            // Apply points to keywords
            $options->apply_points_to_keywords = isset($options->apply_points_to_keywords) ? $options->apply_points_to_keywords : 'off';
            $apply_points_to_keywords = (isset($options->apply_points_to_keywords) && $options->apply_points_to_keywords == 'on') ? true : false;

            // Send Mail to the site Admin too
            $options->send_mail_to_site_admin = ! isset( $options->send_mail_to_site_admin ) ? 'on' : $options->send_mail_to_site_admin;
            $send_mail_to_site_admin = (isset($options->send_mail_to_site_admin) && $options->send_mail_to_site_admin == 'on') ? true : false;

            // Send mail to USER by pass score
            $options->enable_send_mail_to_user_by_pass_score = ! isset( $options->enable_send_mail_to_user_by_pass_score ) ? 'off' : sanitize_text_field( $options->enable_send_mail_to_user_by_pass_score );
            $enable_send_mail_to_user_by_pass_score = (isset($options->enable_send_mail_to_user_by_pass_score) && $options->enable_send_mail_to_user_by_pass_score == 'on') ? true : false;

            // Send mail to ADMIN by pass score
            $options->enable_send_mail_to_admin_by_pass_score = ! isset( $options->enable_send_mail_to_admin_by_pass_score ) ? 'off' : sanitize_text_field( $options->enable_send_mail_to_admin_by_pass_score );
            $enable_send_mail_to_admin_by_pass_score = (isset($options->enable_send_mail_to_admin_by_pass_score) && $options->enable_send_mail_to_admin_by_pass_score == 'on') ? true : false;

            // Information form
            $information_form = (isset($options->information_form) && $options->information_form != '') ? $options->information_form : 'disable';

            // Allow collecting logged in users data
            $options->allow_collecting_logged_in_users_data = isset($options->allow_collecting_logged_in_users_data) ? $options->allow_collecting_logged_in_users_data : 'off';
            $allow_collecting_logged_in_users_data = (isset($options->allow_collecting_logged_in_users_data) && $options->allow_collecting_logged_in_users_data == 'on') ? true : false;

            // Send certificate to admin too
            $options->send_certificate_to_admin = isset($options->send_certificate_to_admin) ? $options->send_certificate_to_admin : 'off';
            $send_certificate_to_admin = (isset($options->send_certificate_to_admin) && $options->send_certificate_to_admin == 'on') ? true : false;

            //Pass score count
            $pass_score_count = (isset($options->pass_score) && $options->pass_score != '') ? absint(intval($options->pass_score)) : 0;

            // Display Interval by
            $display_score_by = (isset($options->display_score_by) && $options->display_score_by != '') ? $options->display_score_by : 'by_percentage';

            // Show information form to logged in users
            $options->show_information_form = isset($options->show_information_form) ? $options->show_information_form : 'on';
            $show_information_form = (isset($options->show_information_form) && $options->show_information_form == 'on') ? true : false;

            // Enable Top Keywords
            $options->enable_top_keywords = isset($options->enable_top_keywords) ? $options->enable_top_keywords : 'off';
            $enable_top_keywords = (isset($options->enable_top_keywords) && $options->enable_top_keywords == 'on') ? true : false;

            // Pass Score Text
            $pass_score_message = '';
            if(isset($options->pass_score_message) && $options->pass_score_message != ''){
                $pass_score_message = Quiz_Maker_Data::ays_autoembed($options->pass_score_message);
            }else{
                $pass_score_message = '<h4 style="text-align: center;">'. __("Congratulations!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You passed the quiz!", $this->plugin_name) .'</p>';
            }

            // Fail Score Text
            $fail_score_message = '';
            if(isset($options->fail_score_message) && $options->fail_score_message != ''){
                $fail_score_message = Quiz_Maker_Data::ays_autoembed($options->fail_score_message);
            }else{
                $fail_score_message = '<h4 style="text-align: center;">'. __("Oops!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You are not passed the quiz! <br> Try again!", $this->plugin_name) .'</p>';
            }

            //Enable Bulk Coupon
            $options->quiz_enable_coupon = isset($options->quiz_enable_coupon) ? sanitize_text_field($options->quiz_enable_coupon) : 'off';
            $quiz_enable_coupon = (isset($options->quiz_enable_coupon) && $options->quiz_enable_coupon == 'on') ? true : false;

            if($allow_collecting_logged_in_users_data){
                if($information_form == 'disable'){
                    $user = wp_get_current_user();
                    if($user->ID != 0){
                        $_REQUEST['ays_user_email'] = $user->data->user_email;
                        $_REQUEST['ays_user_name'] = $user->data->display_name;
                    }
                }
            }

            if(! $show_information_form){
                if($information_form !== 'disable'){
                    $user = wp_get_current_user();
                    if($user->ID != 0){
                        $_REQUEST['ays_user_email'] = $user->data->user_email;
                        $_REQUEST['ays_user_name'] = $user->data->display_name;
                    }
                }
            }

            // Check RTL direction
            $enable_rtl_direction = (isset($options->enable_rtl_direction) && $options->enable_rtl_direction == 'on') ? true : false;

            // MailChimp
            $quiz_settings = new Quiz_Maker_Settings_Actions('Quiz Maker');
            $mailchimp_res = ($quiz_settings->ays_get_setting('mailchimp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('mailchimp');
            $mailchimp = json_decode($mailchimp_res, true);
            $mailchimp_username = isset($mailchimp['username']) ? $mailchimp['username'] : '' ;
            $mailchimp_api_key = isset($mailchimp['apiKey']) ? $mailchimp['apiKey'] : '' ;
            
            $enable_mailchimp = (isset($options->enable_mailchimp) && $options->enable_mailchimp == 'on') ? true : false;
            $enable_double_opt_in = (isset($options->enable_double_opt_in) && $options->enable_double_opt_in == 'on') ? true : false;
            $mailchimp_list = (isset($options->mailchimp_list)) ? $options->mailchimp_list : '';
            $mailchimp_email = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email( $_REQUEST['ays_user_email'] ) : "";
            $user_name = explode(" ", stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) );
            $mailchimp_fname = (isset($user_name[0]) && $user_name[0] != "") ? $user_name[0] : "";
            $mailchimp_lname = (isset($user_name[1]) && $user_name[1] != "") ? $user_name[1] : "";
            
            // Campaign Monitor
            $monitor_res     = ($quiz_settings->ays_get_setting('monitor') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('monitor');
            $monitor         = json_decode($monitor_res, true);
            $monitor_client  = isset($monitor['client']) ? $monitor['client'] : '';
            $monitor_api_key = isset($monitor['apiKey']) ? $monitor['apiKey'] : '';
            $enable_monitor  = (isset($options->enable_monitor) && $options->enable_monitor == 'on') ? true : false;
            $monitor_list    = (isset($options->monitor_list)) ? $options->monitor_list : '';
            $monitor_email   = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $monitor_name    = stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) );

            // ActiveCampaign
            $active_camp_res        = ($quiz_settings->ays_get_setting('active_camp') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('active_camp');
            $active_camp            = json_decode($active_camp_res, true);
            $active_camp_url        = isset($active_camp['url']) ? $active_camp['url'] : '';
            $active_camp_api_key    = isset($active_camp['apiKey']) ? $active_camp['apiKey'] : '';
            $enable_active_camp     = (isset($options->enable_active_camp) && $options->enable_active_camp == 'on') ? true : false;
            $active_camp_list       = (isset($options->active_camp_list)) ? $options->active_camp_list : '';
            $active_camp_automation = (isset($options->active_camp_automation)) ? $options->active_camp_automation : '';
            $active_camp_email      = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $user_name              = explode(" ", stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) );
            $active_camp_fname      = (isset($user_name[0]) && $user_name[0] != "") ? $user_name[0] : "";
            $active_camp_lname      = (isset($user_name[1]) && $user_name[1] != "") ? $user_name[1] : "";
            $active_camp_phone      = sanitize_text_field($_REQUEST['ays_user_phone']);
            
            // Zapier
            $zapier_res    = ($quiz_settings->ays_get_setting('zapier') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('zapier');
            $zapier        = json_decode($zapier_res, true);
            $enable_zapier = (isset($options->enable_zapier) && $options->enable_zapier == 'on') ? true : false;
            $zapier_hook   = isset($zapier['hook']) ? $zapier['hook'] : '';
            $zapier_data   = array();

            $zapier_data['Email'] = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $zapier_data['Name']  = isset($_REQUEST['ays_user_name']) ? stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) : "";
            $zapier_data['Phone'] = isset($_REQUEST['ays_user_phone']) ? sanitize_text_field($_REQUEST['ays_user_phone']) : "";

            $zapier_flag = false;
            if($zapier_data['Email'] == "" && $zapier_data['Name'] == "" && $zapier_data['Phone'] == ""){
                $zapier_flag = true;
            }

            foreach ( $quiz_attributes as $key => $attr ) {
                if (array_key_exists($attr->slug, $_REQUEST) && $_REQUEST[$attr->slug] != "") {
                    $zapier_data[$attr->name] = sanitize_text_field($_REQUEST[$attr->slug]);
                }
            }

            // Slack
            $slack_res          = ($quiz_settings->ays_get_setting('slack') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('slack');
            $slack              = json_decode($slack_res, true);
            $enable_slack       = (isset($options->enable_slack) && $options->enable_slack == 'on') ? true : false;
            $slack_conversation = (isset($options->slack_conversation)) ? $options->slack_conversation : '';
            $slack_token        = isset($slack['token']) ? $slack['token'] : '';
            $slack_data         = array();

            $slack_data['Name']   = isset($_REQUEST['ays_user_name']) ? stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) : "";
            $slack_data['E-mail'] = (isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "") ? sanitize_email($_REQUEST['ays_user_email']) : "";
            $slack_data['Phone']  = isset($_REQUEST['ays_user_phone']) ? sanitize_text_field($_REQUEST['ays_user_phone']) : "";
                        
            foreach ( $quiz_attributes as $key => $attr ) {
                if (array_key_exists($attr->slug, $_REQUEST) && $_REQUEST[$attr->slug] != "") {
                    $slack_data[$attr->name] = sanitize_text_field($_REQUEST[$attr->slug]);
                }
            }

            // Google Sheets
            $google_res           = ($quiz_settings->ays_get_setting('google') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('google');
            $google               = json_decode($google_res, true);
            $enable_google        = (isset($options->enable_google_sheets) && $options->enable_google_sheets == 'on') ? true : false;
            $google_sheet_custom_fields = (isset($options->google_sheet_custom_fields) && $options->google_sheet_custom_fields != '') ? $options->google_sheet_custom_fields : array();
            $sheet_id             = (isset($options->spreadsheet_id) && $options->spreadsheet_id != '') ? $options->spreadsheet_id : '';
            $quiz_id              = (isset($_REQUEST['ays_quiz_id']) && $_REQUEST['ays_quiz_id'] != '') ? $_REQUEST['ays_quiz_id'] : '';
            $google_token         = isset($google['token']) ? $google['token'] : '';
            $google_refresh_token = isset($google['refresh_token']) ? $google['refresh_token'] : '';
            $google_client_id     = isset($google['client']) ? $google['client'] : '';
            $google_client_secret = isset($google['secret']) ? $google['secret'] : '';
            $google_data = array(
                "refresh_token" => $google_refresh_token,
                "client_id"     => $google_client_id,
                "client_secret" => $google_client_secret,
                "sheed_id"      => $sheet_id,
                "custom_fields" => $google_sheet_custom_fields,
                'id'            => $quiz_id,
                'quiz_attributes' => array(),
            );

            foreach ( $quiz_attributes as $key => $attr ) {
                if (array_key_exists($attr->slug, $_REQUEST) && $_REQUEST[$attr->slug] != "") {
                    $google_data['quiz_attributes'][$attr->slug] = sanitize_text_field($_REQUEST[$attr->slug]);
                }
            }

            // General Setting's Options
            $general_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
            $settings_options = json_decode( stripslashes( $general_settings_options ), true );

            // Do not store IP adressess 
            $disable_user_ip = (isset($settings_options['disable_user_ip']) && $settings_options['disable_user_ip'] == 'on') ? true : false;
            
            // Limit user
            $options->limit_users = isset($options->limit_users) ? $options->limit_users : 'off';
            $limit_users = (isset($options->limit_users) && $options->limit_users == 'on') ? true : false;

            // Limit user by
            $limit_users_by = (isset($options->limit_users_by) && $options->limit_users_by != '') ? $options->limit_users_by : 'ip';

            $quiz_max_pass_count = (isset( $options->quiz_max_pass_count ) && $options->quiz_max_pass_count != '') ? absint( intval( $options->quiz_max_pass_count ) ) : 1;

            // Quiz Title
            $quiz_title = (isset($quiz['title']) && $quiz['title'] != '') ? stripslashes( $quiz['title'] ) : '';

            // Keyword Default Max Value
            $keyword_default_max_value = (isset($settings_options['keyword_default_max_value']) && $settings_options['keyword_default_max_value'] != '') ? absint($settings_options['keyword_default_max_value']) : 6;



            // User explanation            

            if(isset($_REQUEST['user-answer-explanation']) && count($_REQUEST['user-answer-explanation']) != 0){
                $user_explanation = $_REQUEST['user-answer-explanation'];
            }else{
                $user_explanation = array();
            }

            $questions_count = count($question_ids);
            $correctness = array();
            $user_answered = array();
            $correctness_results = array();
            $answer_max_weights = array();

            if (is_array($questions_answers)) {
                $quests = array();
                $questions_cats = array();
                $quiz_questions_ids = array();
                $question_bank_by_categories1 = array();

                foreach($questions_answers as $key => $val){
                    $question_id = explode('-', $key)[2];
                
                    $quiz_questions_ids[] = strval($question_id);
                }
            
                $questions_categories = Quiz_Maker_Data::get_questions_categories( implode( ',', $quiz_questions_ids ) );
                $quest_s = Quiz_Maker_Data::get_quiz_questions_by_ids($quiz_questions_ids);
                foreach($quest_s as $quest){
                    $quests[$quest['id']] = $quest;
                }

                foreach($quiz_questions_ids as $key => $question_id){
                    $questions_cats[$quests[$question_id]['category_id']][$question_id] = null;
                }

                $keywords_arr = array();
                $points_keywords_arr = array();
                foreach ($questions_answers as $key => $questions_answer) {
                    $continue = false;
                    $question_id = explode('-', $key)[2];
                    if(Quiz_Maker_Data::is_question_not_influence($question_id)){
                        $questions_count--;
                        $continue = true;
                    }
                    $multiple_correctness = array();
                    $keyword_points_sum = array();
                    $has_multiple = Quiz_Maker_Data::has_multiple_correct_answers($question_id);
                    $is_checkbox = Quiz_Maker_Data::is_checkbox_answer($question_id);
                    $answer_max_weights[$question_id] = Quiz_Maker_Data::get_answers_max_weight($question_id, $is_checkbox);
                    
                    $user_answered["question_id_" . $question_id] = $questions_answer;

                    if($is_checkbox){
                    $has_multiple = true;
                    }

                    if ($has_multiple) {
                        if (is_array($questions_answer)) {
                            foreach ($questions_answer as $answer_id) {
                                $multiple_correctness[] = Quiz_Maker_Data::check_answer_correctness($question_id, $answer_id, $calculate_score);
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $answer_id);
                                $keyword_points_sum[] = Quiz_Maker_Data::check_answer_correctness($question_id, $answer_id, 'by_points');
                                if(!is_null($answer_keyword) && $answer_keyword != false){
                                    $keywords_arr[] = $answer_keyword;
                                    $points_keywords_arr[$question_id][] = array(
                                        'keyword' => $answer_keyword,
                                        'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $answer_id, 'by_points'),
                                    );
                                }
                            }
                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = array_sum($multiple_correctness);
                                }
                                $correctness_results["question_id_" . $question_id] = array_sum($multiple_correctness);
                                continue;
                            }
                            
                            if($strong_count_checkbox === false){
                                if(!$continue){
                                    $correctness[$question_id] = $this->isHomogenousStrong($multiple_correctness, $question_id);
                                }
                                $correctness_results["question_id_" . $question_id] = $this->isHomogenousStrong($multiple_correctness, $question_id);
                            }else{
                                if ($this->isHomogenous($multiple_correctness, $question_id)) {
                                    if(!$continue){
                                        $correctness[$question_id] = true;
                                    }
                                    $correctness_results["question_id_" . $question_id] = true;
                                } else {
                                    if(!$continue){
                                        $correctness[$question_id] = false;
                                    }
                                    $correctness_results["question_id_" . $question_id] = false;
                                }
                            }
                        } else {
                            $questions_answer_keyword = $questions_answer;
                            if( intval( $questions_answer_keyword ) != 0 ){
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer_keyword);
                                if(!is_null($answer_keyword) && $answer_keyword != false){
                                    $keywords_arr[] = $answer_keyword;
                                    $points_keywords_arr[$question_id] = array(
                                        'keyword' => $answer_keyword,
                                        'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, 'by_points'),
                                    );
                                }
                            }
                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                }
                                $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer);
                                $points_keywords_arr[$question_id] = array(
                                    'keyword' => $answer_keyword,
                                    'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, 'by_points'),
                                );
                                $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                continue;
                            }
                            if($strong_count_checkbox === false){
                                if(Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score)){
                                    if(!$continue){
                                        $correctness[$question_id] = 1 / intval(Quiz_Maker_Data::count_multiple_correct_answers($question_id));
                                    }
                                }else{
                                    if(!$continue){
                                        $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                    }
                                }
                                $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                            }else{
                                //if(!$continue){
                                //    $correctness[$question_id] = false;
                                //}
                                //$correctness_results["question_id_" . $question_id] = false;

                                $questions_answer = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                if(!$continue){
                                    $correctness[$question_id] = $this->isHomogenous( array( $questions_answer ), $question_id );
                                }
                                $correctness_results["question_id_" . $question_id] = $this->isHomogenous( array( $questions_answer ), $question_id );
                            }
                        }
                    } elseif(Quiz_Maker_Data::has_text_answer($question_id)) {
                        $quests_data = ( isset( $quests[$question_id] ) && ! empty( $quests[$question_id] ) ) ? $quests[$question_id] : array();
                        $quests_data_options = isset( $quests_data['options'] ) ? json_decode( $quests_data['options'], true ) : array();

                        if(!$continue){
                            $correctness[$question_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer, $calculate_score, $quests_data_options);
                        }
                        $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_text_answer_correctness($question_id, $questions_answer, $calculate_score, $quests_data_options);
                    } else {
                        if(!$continue){
                            $correctness[$question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        }
                        $correctness_results["question_id_" . $question_id] = Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        if( intval( $questions_answer ) != 0 ){
                            $answer_keyword = Quiz_Maker_Data::get_correct_answer_keyword($question_id, $questions_answer);
                            if(!is_null($answer_keyword) && $answer_keyword != false){
                                $keywords_arr[] = $answer_keyword;
                                $points_keywords_arr[$question_id] = array(
                                    'keyword' => $answer_keyword,
                                    'point'   => Quiz_Maker_Data::check_answer_correctness($question_id, $questions_answer, 'by_points'),
                                );
                            }
                        }
                    }
                }
                
                $new_correctness = array();
                $quiz_weight_correctness = array();
                $quiz_weight_points = array();
                $corrects_count = 0;
                $corrects_count_by_cats = array();
                foreach($questions_cats as $cat_id => &$q_ids){
                    $corrects_count_by_cats[$cat_id] = 0;
                    foreach($correctness as $question_id => $item){
                        if( array_key_exists( strval($question_id), $q_ids ) ){
                            switch($calculate_score){
                                case "by_correctness":
                                    if($item){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                                case "by_points":
                                    if($item == floatval($answer_max_weights[$question_id])){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                                default:
                                    if($item){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                            }
                        }
                    }
                }

                foreach($correctness as $question_id => $item){
                    $question_weight = Quiz_Maker_Data::get_question_weight($question_id);
                    $new_correctness[strval($question_id)] = $question_weight * floatval($item);
                    $quiz_weight_points[strval($question_id)] = $question_weight * floatval($answer_max_weights[$question_id]);
                    $quiz_weight_correctness[strval($question_id)] = $question_weight;
                    switch($calculate_score){
                        case "by_correctness":
                            if($item){
                                $corrects_count++;
                            }
                        break;
                        case "by_points":
                            if($item == floatval($answer_max_weights[$question_id])){
                                $corrects_count++;
                            }
                        break;
                        default:
                            if($item){
                                $corrects_count++;
                            }
                        break;
                    }
                }

                $quiz_weight_new_correctness_by_cats = array();
                $quiz_weight_correctness_by_cats = array();
                $quiz_weight_points_by_cats = array();

                $questions_count_by_cats = array();
                foreach($questions_cats as $cat_id => &$q_ids){
                    foreach($q_ids as $q_id => &$val){
                        $val = array_key_exists($q_id, $new_correctness) ? $new_correctness[$q_id] : false;
                        $quiz_weight_new_correctness_by_cats[$cat_id][$q_id] = $val;
                        if( Quiz_Maker_Data::is_question_not_influence($q_id) ){
                            continue;
                        }

                        if ( isset( $quiz_weight_correctness[$q_id] ) && sanitize_text_field( $quiz_weight_correctness[$q_id] ) != '' ) {
                            $quiz_weight_correctness_by_cats[$cat_id][$q_id] = $quiz_weight_correctness[$q_id];
                        }
                        if ( isset( $quiz_weight_points[$q_id] ) && sanitize_text_field( $quiz_weight_points[$q_id] ) != '' ) {
                            $quiz_weight_points_by_cats[$cat_id][$q_id] = $quiz_weight_points[$q_id];
                        }

                    }
                    $questions_count_by_cats[$cat_id] = count($q_ids);
                }

                $final_score_by_cats = array();
                $quiz_weight_cats = array();
                $correct_answered_count_cats = array();
                $cat_score_is_decimal = false;
                $final_score_is_decimal = false;
                foreach($quiz_weight_new_correctness_by_cats as $cat_id => $q_ids){

                    if ( ! isset( $quiz_weight_correctness_by_cats[$cat_id] ) ) {
                        continue;
                    }
                    $quiz_weight_correctness_by_cats[$cat_id] = array_filter($quiz_weight_correctness_by_cats[$cat_id], "strlen");

                    switch($calculate_score){
                        case "by_correctness":
                            $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                        break;
                        case "by_points":
                            $quiz_weight_cat = array_sum($quiz_weight_points_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_points_by_cats[$cat_id]);
                        break;
                        default:
                            $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                        break;
                    }

    //                    $correct_answered_count_cat = array_sum($q_ids);
                    $correct_answered_count_cats[$cat_id] = array_sum($q_ids);

                    if($quiz_weight_cat == 0){
                        $final_score_by_cats[$cat_id] = floatval(0);
                    }else{
    //                        $final_score_by_cats[$cat_id] = floatval(floor(($correct_answered_count_cat / $quiz_weight_cat) * 100));
                        $final_score_by_cats[$cat_id] = floatval(floor((intval($correct_answered_count_cats[$cat_id]) / intval($quiz_weight_cat) ) * 100));
                        $final_score_by_cats[$cat_id] = round($final_score_by_cats[$cat_id], 2);
                    }
                }

                switch($calculate_score){
                    case "by_correctness":
                        $quiz_weight = array_sum($quiz_weight_correctness);
                    break;
                    case "by_points":
                        $quiz_weight = array_sum($quiz_weight_points);
                    break;
                    default:
                        $quiz_weight = array_sum($quiz_weight_correctness);
                    break;
                }
                $correct_answered_count = array_sum($new_correctness);
                
                if($quiz_weight == 0){
                    $final_score = floatval(0);
                }else{
                    $final_score = floatval( ( $correct_answered_count / $quiz_weight ) * 100 );
                    $final_score = round( $final_score, 2 );
                }

                $score_by_cats = array();
                foreach($final_score_by_cats as $cat_id => $cat_score){
                    switch($display_score){
                        case "by_correctness":
                            $score_by_cats[$cat_id] = array(
                                'score' => $corrects_count_by_cats[$cat_id] . " / " . $questions_count_by_cats[$cat_id],
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        case "by_points":
                            $score_by_cats[$cat_id] = array(
    //                                'score' => $correct_answered_count_cat[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                                'score' => $correct_answered_count_cats[$cat_id] . " / " . $quiz_weight_cats[$cat_id],
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        case "by_percentage":
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        default:
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                    }
                }

                switch($display_score){
                    case "by_correctness":
                        $score = $corrects_count . " / " . $questions_count;
                    break;
                    case "by_points":
                        $score = $correct_answered_count . " / " . $quiz_weight;
                    break;
                    case "by_percentage":
                        $score = $final_score . "%";
                    break;
                    default:
                        $score = $final_score . "%";
                    break;
                }

                $wrong_answered_count = $questions_count - $corrects_count;

                $skipped_questions_count = 0;
                foreach ($user_answered as $q_id => $user_answered_val) {
                    $question_id_val = explode('_', $q_id)[2];
                    if(Quiz_Maker_Data::is_question_not_influence($question_id_val)){
                        continue;
                    }

                    if ( $user_answered_val == '') {
                        $skipped_questions_count++;
                    }
                }

                $only_wrong_answers_count = $questions_count - ( $corrects_count + $skipped_questions_count );
                $answered_questions_count = $questions_count - $skipped_questions_count;
                $user_failed_questions_count = $corrects_count + ( $questions_count - ($corrects_count + $skipped_questions_count) );

                if ( ! empty( $user_failed_questions_count ) || $user_failed_questions_count != 0) {
                    $score_by_answered_questions = round( ( $corrects_count * 100 ) / $user_failed_questions_count , 1 );
                } else {
                    $score_by_answered_questions = 0;
                }

                $hide_result = false;
                $finish_text = null;
                $interval_msg = null;
                $interval_message = '';
                $interval_image = null;
                $product_id = null;

                //if (isset($options->enable_result) && $options->enable_result == "on") {
                //    $text = $options->result_text;
                //    $hide_result = true;
                //}
                //if($finish_text == ''){
                //    $finish_text = null;
                //}

                if(empty($score_by_cats)){
                    $result_score_by_categories = '';
                }else{
                    $result_score_by_categories = '<div class="ays_result_by_cats">';
                    foreach($score_by_cats as $cat_id => $cat){
                        $result_score_by_categories .= '<p class="ays_result_by_cat">
                            <strong class="ays_result_by_cat_name">'. $cat['categoryName'] .':</strong>
                            <span class="ays_result_by_cat_score">'. $cat['score'] .'</span>
                        </p>';
                    }
                    $result_score_by_categories .= '</div>';
                    $result_score_by_categories = str_replace(array("\r\n", "\n", "\r"), "", $result_score_by_categories);
                }

                $score_by = ($display_score_by == 'by_percentage') ? $final_score : intval($correct_answered_count);
                $score_by = '';
                switch ($display_score_by) {
                    case 'by_percentage':
                        $score_by = $final_score;
                        break;
                    case 'by_points':
                        $score_by = floatval( $correct_answered_count );
                        break;
                    case 'by_keywords':
                        if($apply_points_to_keywords){
                            $points_keywords_full_arr = array();
                            $points_sum_keywords_arr = array();

                            foreach ($points_keywords_arr as $id => $points_keywords) {
                                if(!array_key_exists('keyword', $points_keywords)){
                                    foreach ($points_keywords as $key => $value) {
                                        $points_keywords_full_arr[] = $value;
                                    }
                                }else{
                                $points_keywords_full_arr[] = $points_keywords;
                                }
                            }

                            foreach ($points_keywords_full_arr as $id => $points_keywords) {
                                $points_sum_keywords_arr[$points_keywords['keyword']] = 0;
                            }

                            foreach ($points_keywords_full_arr as $id => $points_keywords) {
                                $points_sum_keywords_arr[$points_keywords['keyword']] += $points_keywords['point'];
                            }
                            if( is_array( $points_keywords_full_arr ) ){
                                $max_keywords_answered_count = max( $points_sum_keywords_arr );
                                $max_keywords_answered_keyword = array_search( $max_keywords_answered_count, $points_sum_keywords_arr );
                                $score_by = $max_keywords_answered_keyword;
                            }else{
                                $score_by = "";
                            }
                        }else{
                            if( is_array( $keywords_arr ) ){
                                $keywords_count_arr = array_count_values($keywords_arr);
                                if ( ! empty( $keywords_count_arr ) ) {
                                    $max_keywords_answered_count = max( $keywords_count_arr );
                                    $max_keywords_answered_keyword = array_search( $max_keywords_answered_count, $keywords_count_arr );
                                    $score_by = $max_keywords_answered_keyword;
                                } else {
                                    $score_by = "";
                                }
                            }else{
                                $score_by = "";
                            }
                        }
                        break;
                    default:
                        $score_by = $final_score;
                        break;
                }

                $interval_flag = false;
                foreach ($quiz_intervals as $quiz_interval) {
                    $quiz_interval = (array)$quiz_interval;
                    if($display_score_by == 'by_keywords'){
                        if ($quiz_interval['interval_keyword'] == $score_by) {
                            $interval_flag = true;
                        }
                    }else{
                        if ( floatval( $quiz_interval['interval_min'] ) <= $score_by && $score_by <= floatval( $quiz_interval['interval_max'] ) ) {
                            $interval_flag = true;
                        }
                    }

                    if($interval_flag){
                        $interval_msg = Quiz_Maker_Data::ays_autoembed( $quiz_interval['interval_text'] );
                        $interval_image = $quiz_interval['interval_image'];
                        $product_id = $quiz_interval['interval_wproduct'];
                        $interval_redirect_url = $quiz_interval['interval_redirect_url'];
                        $interval_redirect_delay = $quiz_interval['interval_redirect_delay'];

                        $interval_message = "";
                        $interval_redirect_after = "";
                        $intimg = false;
                        $intmsg = false;
                        if($interval_image !== null && $interval_image != ''){
                            $intimg = true;
                            $interval_message .= "<div style='width:100%;max-width:400px;margin:10px auto;'>";
                            $interval_message .= "<img style='max-width:100%;' src='".$interval_image."'>";
                            $interval_message .= "</div>";
                        }
                        if($interval_msg !== null && $interval_msg != ''){
                            $intmsg = true;
                            $interval_message .= "<div>" . $interval_msg . "</div>";
                        }
                        if($intimg || $intmsg){
                            $interval_message = "<div>" . $interval_message . "</div>";
                        }
                        if($interval_redirect_url !== null && $interval_redirect_url != ''){
                            if($interval_redirect_delay == ''){
                            $interval_redirect_delay = 0;
                            }
                            $interval_redirect_after = Quiz_Maker_Data::secondsToWords($interval_redirect_delay);
                        }
                        break;
                    }
                }

                
                $correctness_and_answers = array(
                    'correctness' => $correctness_results,
                    'user_answered' => $user_answered
                );

                $not_influence_m = array();
                foreach ($quest_s as $key => $value) {
                    $not_influence_m[] = $quest_s[$key]['not_influence_to_score'];
                }


                $quiz_logo = "";
                if($quiz_image !== ""){
                    $quiz_logo = '<img src="'.$quiz_image.'" alt="Quiz logo" title="Quiz logo">';
                }

                $user_first_name = '';
                $user_last_name = '';
                $user_id = $_REQUEST['user_id'];
                if($user_id != 0){
                    $usermeta = get_user_meta( $user_id );
                    if($usermeta !== null){
                        $user_first_name = (isset($usermeta['first_name'][0]) && sanitize_text_field( $usermeta['first_name'][0] != '') ) ? sanitize_text_field( $usermeta['first_name'][0] ) : '';
                        $user_last_name = (isset($usermeta['last_name'][0]) && sanitize_text_field( $usermeta['last_name'][0] != '') ) ? sanitize_text_field( $usermeta['last_name'][0] ) : '';
                    }
                }

                $active_coupon = '';
                if ( $quiz_enable_coupon ) {
                    $active_coupon = Quiz_Maker_Data::ays_quiz_get_active_coupon( $quiz_id, $options );
                }

                $result_unique_code = strtoupper( uniqid() );

                $message_data = array(
                    'quiz_name' => stripslashes($quiz['title']),
                    'user_name' => stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ),
                    'user_email' => sanitize_email( $_REQUEST['ays_user_email'] ),
                    'user_pass_time' => Quiz_Maker_Data::get_time_difference(sanitize_text_field($_REQUEST['start_date']), sanitize_text_field($_REQUEST['end_date'])),
                    'quiz_time' => Quiz_Maker_Data::secondsToWords($options->timer),
                    'score' => $final_score . "%",
                    'user_points' => $correct_answered_count,
                    'max_points' => $quiz_weight,
                    'user_corrects_count' => $corrects_count,
                    'questions_count' => $questions_count,
                    'quiz_logo' => $quiz_logo,
                    'avg_score' => Quiz_Maker_Data::ays_get_average_of_scores($quiz_id) . "%",
                    'avg_rate' => round(Quiz_Maker_Data::ays_get_average_of_rates($quiz_id), 1),
                    'current_date' => date_i18n( get_option( 'date_format' ), strtotime( sanitize_text_field( $_REQUEST['end_date'] ) ) ),
                    'results_by_cats' => $result_score_by_categories,
                    'unique_code' => $result_unique_code,
                    'wrong_answers_count' => $wrong_answered_count,
                    'not_answered_count' => $skipped_questions_count,
                    'skipped_questions_count' => $skipped_questions_count,
                    'answered_questions_count' => $answered_questions_count,
                    'score_by_answered_questions' => $score_by_answered_questions,
                    'user_first_name' => $user_first_name,
                    'user_last_name' => $user_last_name,
                    'only_wrong_answers_count' => $only_wrong_answers_count,
                    'quiz_coupon' => $active_coupon,
                );

                $all_mv_keywords_arr = Quiz_Maker_Data::ays_quiz_generate_keyword_array($keyword_default_max_value);
                $mv_keyword_counts = array_count_values($keywords_arr);

                foreach ($all_mv_keywords_arr as $key => $value) {
                    $mv_keyword_percentage = 0;
                    $total_keywords_count = array_sum($mv_keyword_counts);
                    if($total_keywords_count > 0){
                        $mv_keyword_percentage = ( $mv_keyword_counts[$value] / $total_keywords_count ) * 100;
                    }
                    if( array_key_exists( $value, $mv_keyword_counts) ){
                        $message_data[ 'keyword_count_' . $value ] = $mv_keyword_counts[$value];
                        $message_data[ 'keyword_percentage_' . $value ] = round($mv_keyword_percentage,2) .'%';
                    }else{
                        $message_data[ 'keyword_count_' . $value ] = 0;
                        $message_data[ 'keyword_percentage_' . $value ] = 0 . '%';
                    }

                }

                if($enable_top_keywords){
                    $assign_keywords_count_arr = array();
                    $assign_keywords_percentage_arr = array();
                    foreach ($all_mv_keywords_arr as $key => $value) {
                        $top_keyword_percentage = 0;
                        $total_top_keywords_count = array_sum($mv_keyword_counts);
                        if($total_top_keywords_count > 0){
                            $top_keyword_percentage = ( $mv_keyword_counts[$value] / $total_top_keywords_count ) * 100;
                        }

                        if( array_key_exists( $value, $mv_keyword_counts) ){
                            $assign_keywords_count_arr[$value]['keyword_count'] = $mv_keyword_counts[$value];
                            $assign_keywords_percentage_arr[$value]['keyword_percentage'] = round($top_keyword_percentage,2);
                        }
                    }

                    $assign_keywords_obj = (isset($options->assign_keywords) && !empty($options->assign_keywords)) ?  $options->assign_keywords : array();


                    foreach ($assign_keywords_obj as $key => $value) {
                        if( array_key_exists( $value->assign_top_keyword, $assign_keywords_count_arr) ){
                            $assign_keywords_count_arr[$value->assign_top_keyword]['keyword_text'] = $value->assign_top_keyword_text;
                        }

                        if( array_key_exists( $value->assign_top_keyword, $assign_keywords_percentage_arr) ){
                            $assign_keywords_percentage_arr[$value->assign_top_keyword]['keyword_text'] = $value->assign_top_keyword_text;

                        }
                    }

                    usort($assign_keywords_count_arr, array( $this, 'sortByOrderTopKeywords' ) );
                    usort($assign_keywords_percentage_arr, array( $this, 'sortByOrderTopKeywords' ) );

                    $message_data[ 'top_keywords_count' ] = $assign_keywords_count_arr;
                    $message_data[ 'top_keywords_percentage' ] = $assign_keywords_percentage_arr;
                }

                $interval_message_for_cert = Quiz_Maker_Data::replace_message_variables($interval_message, $message_data);
                $message_data['interval_message'] = $interval_message_for_cert;

                $quiz_attributes_information = array();
                foreach ($quiz_attributes as $attribute) {
                    $attr_value = (isset($_REQUEST[strval($attribute->slug)])) ? $_REQUEST[strval($attribute->slug)] : '';
                    $quiz_attributes_information[strval($attribute->name)] = $attr_value;
                    $message_data[$attribute->slug] = $attr_value;
                }

                if($disable_user_ip){
                    $user_ip = '';
                }else{
                    $user_ip = Quiz_Maker_Data::get_user_ip();
                }

                $data = array(
                    'user_ip' => $user_ip,
                    'user_name' => stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ),
                    'user_email' => sanitize_email( $_REQUEST['ays_user_email'] ),
                    'user_phone' => stripslashes( sanitize_text_field( $_REQUEST['ays_user_phone'] ) ),
                    'start_date' => esc_sql($_REQUEST['start_date']),
                    'end_date' => esc_sql($_REQUEST['end_date']),
                    'answered' => $correctness_and_answers,
                    'score' => $final_score,
                    'quiz_id' => absint(intval($_REQUEST["quiz_id"])),
                    'user_explanation' => $user_explanation,
                    'calc_method' => $calculate_score,
                    'user_points' => $correct_answered_count,
                    'max_points' => $quiz_weight,
                    'user_corrects_count' => $corrects_count,
                    'questions_count' => $questions_count,
                    'attributes_information' => $quiz_attributes_information,
                    'unique_code' => $result_unique_code,
                    'rtl_direction' => $enable_rtl_direction,
                    'started_status' => $limited_result_id,
                    'mv_keywords_counts' => $mv_keyword_counts,
                    'quiz_coupon' => $active_coupon,
                    'chained_quiz_id' => $chained_quiz_id,
                );
                
                $nsite_url_base = get_site_url();
                $nsite_url_replaced = str_replace( array( 'http://', 'https://' ), '', $nsite_url_base );
                $nsite_url = trim( $nsite_url_replaced, '/' );
                //$nsite_url = "levon.com";
                $nno_reply = "noreply@".$nsite_url;

                if(isset($options->email_config_from_name) && $options->email_config_from_name != "") {
                    $uname = stripslashes($options->email_config_from_name);
                } else {
                    $uname = 'Quiz Maker';
                }
                if(isset($options->email_config_from_email) && $options->email_config_from_email != "") {
                    $nfrom = "From: " . $uname . " <".stripslashes($options->email_config_from_email).">";
                }else{
                    $nfrom = "From: " . $uname . " <quiz_maker@".$nsite_url.">";
                }
                if(isset($options->email_config_from_subject) && $options->email_config_from_subject != "") {
                    $subject = stripslashes($options->email_config_from_subject);
                } else {
                    $subject = stripslashes($quiz['title']);
                }
                
                if(isset($options->email_config_replyto_name) && $options->email_config_replyto_name != "") {
                    $replyto_name = stripslashes($options->email_config_replyto_name);
                } else {
                    $replyto_name = '';
                }

                $nreply = "";
                if(isset($options->email_config_replyto_email) && $options->email_config_replyto_email != "") {
                    if(filter_var($options->email_config_replyto_email, FILTER_VALIDATE_EMAIL)){
                        $nreply = "Reply-To: " . $replyto_name . " <".stripslashes($options->email_config_replyto_email).">";
                    }
                }

                $subject = Quiz_Maker_Data::replace_message_variables($subject, $message_data);
                $uname = Quiz_Maker_Data::replace_message_variables($uname, $message_data);
                $replyto_name = Quiz_Maker_Data::replace_message_variables($replyto_name, $message_data);
                
                $send_mail_to_user = isset($options->user_mail) && $options->user_mail == "on" ? true : false;
                $send_mail_to_admin = isset($options->admin_mail) && $options->admin_mail == "on" ? true : false;
                $send_certificate_to_user = isset($options->enable_certificate) && $options->enable_certificate == "on" ? true : false;
                
                // Enable certificate without send
                $options->enable_certificate_without_send = isset( $options->enable_certificate_without_send ) ? $options->enable_certificate_without_send : 'off';
                $enable_certificate_without_send = ( isset( $options->enable_certificate_without_send ) && $options->enable_certificate_without_send == "on" ) ? true : false;

                if( $send_certificate_to_user === true && $enable_certificate_without_send === true ){
                    $send_certificate_to_user = true;
                    $enable_certificate_without_send = false;
                }elseif( $send_certificate_to_user === true && $enable_certificate_without_send === false ){
                    $send_certificate_to_user = true;
                    $enable_certificate_without_send = false;
                }elseif( $send_certificate_to_user === false && $enable_certificate_without_send === true ){
                    $send_certificate_to_user = false;
                    $enable_certificate_without_send = true;
                }elseif( $send_certificate_to_user === false && $enable_certificate_without_send === false ){
                    $send_certificate_to_user = false;
                    $enable_certificate_without_send = false;
                }else{
                    $send_certificate_to_user = false;
                    $enable_certificate_without_send = false;
                }

                $cert = false;
                $force_mail_to_user = false;
                if ($send_certificate_to_user){
                    $cert = true;
                    if($send_mail_to_user){
                        if($options->mail_message == ""){
                            $options->mail_message = "Certificate";
                        }
                    }else{
                        $options->mail_message = "Certificate";
                        $force_mail_to_user = true;
                    }
                    $options->user_mail = "on";
                }

                $send_mail_to_user = isset($options->user_mail) && $options->user_mail == "on" ? true : false;
                
                if( $enable_certificate_without_send === true ){
                    $cert = true;
                }

                $pdf_response = null;
                $pdf_content = null;
                if( $send_mail_to_user || $enable_certificate_without_send ){
                    if($cert && $final_score >= intval($options->certificate_pass)){
                        $cert_title = stripslashes((isset($options->certificate_title)) ? $options->certificate_title : '');
                        $cert_body = Quiz_Maker_Data::ays_autoembed((isset($options->certificate_body)) ? $options->certificate_body : '');
                        $cert_body = Quiz_Maker_Data::ays_autoembed((isset($options->certificate_body)) ? $options->certificate_body : '');
                        $certificate_image = (isset($options->certificate_image) && $options->certificate_image != '') ? $options->certificate_image : '';
                        $certificate_frame = (isset($options->certificate_frame) && $options->certificate_frame != '') ? $options->certificate_frame : 'default';
                        $certificate_orientation = (isset($options->certificate_orientation) && $options->certificate_orientation != '') ? $options->certificate_orientation : 'l';

                        $pdf = new Quiz_PDF_API();
                        $pdfData = array(
                            "type"          => "pdfapi",
                            "cert_title"    => $cert_title,
                            "cert_body"     => $cert_body,
                            "cert_score"    => $final_score,
                            "cert_data"     => $message_data,
                            "cert_user"     => stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ),
                            "cert_quiz"     => stripslashes($quiz['title']),
                            "cert_quiz_id"  => $quiz_id,
                            "cert_image"    => $certificate_image,
                            "cert_frame"    => $certificate_frame,
                            "cert_orientation"    => $certificate_orientation,
                            "current_date"  => current_time( 'Y-m-d H:i:s' ),
                        );
                        $pdf_response = $pdf->generate_PDF($pdfData);
                        $pdf_content = $pdf_response['status'];
                    }
                }

                $conditions_data = array("hasAction" => false);
                if(has_action('ays_qm_conditions_action')){
                    $quiz_conditions = isset($quiz['conditions']) && $quiz['conditions'] != "" ? json_decode($quiz['conditions'], true) : array();
                    $conditions_data = apply_filters( 'ays_qm_conditions_action', $quiz_conditions, $questions_answers );
                    $cond_page_message = "";
                    if(!empty($conditions_data)){
                        $conditions_data['hasAction'] = true;
                        $cond_page_message = isset($conditions_data['pageMessage']) && $conditions_data['pageMessage'] != "" ? $conditions_data['pageMessage'] : "";
                        $cond_email_file_id = isset($conditions_data['email_file_id']) && $conditions_data['email_file_id'] != "" ? $conditions_data['email_file_id'] : "";
                        $cond_email_file = isset($conditions_data['email_file']) && $conditions_data['email_file'] != "" ? $conditions_data['email_file'] : "";
                        $cond_email_message = isset($conditions_data['emailMessage']) && $conditions_data['emailMessage'] != "" ? $conditions_data['emailMessage'] : "";
                        $conditions_data['pageMessage'] = Quiz_Maker_Data::replace_message_variables($cond_page_message, $message_data);
                        $wp_user = null;
                        if( $_REQUEST['user_id']){
                            $wp_user = get_userdata( $_REQUEST['user_id'] );
                        }
                        $c_user_email = isset($_REQUEST['ays_user_email']) && $_REQUEST['ays_user_email'] != "" ? sanitize_email( $_REQUEST['ays_user_email'] ) : "";
                        $c_user_name = isset($_REQUEST['ays_user_name']) && $_REQUEST['ays_user_name'] != "" ? stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) : "";

                        $cond_user_email = $c_user_email == "" ? $wp_user->data->user_email : $c_user_email;
                        $cond_user_name =  $c_user_name  == "" ? $wp_user->data->display_name : $c_user_name;

                        $conditions_email_data = array(
                            "cond_user_email"    => $cond_user_email,
                            "cond_user_name"     => $cond_user_name,
                            "cond_email_file_id" => $cond_email_file_id,
                            "cond_email_message"  => $cond_email_message,
                            "from"     => $nfrom,
                            "reply_to" => $nreply,
                            "subject"  => $subject,
                            "message_data"  => $message_data,
                        );
                        if($cond_email_file || $cond_email_message){
                            do_action("ays_qm_conditions_send_email", $conditions_email_data);
                        }
                    }
                    else{
                        $conditions_data['hasAction'] = false;
                    }
                }

                // Disabling store data in DB
                $download_certificate_html = "";
                if($disable_store_data){
                    if($pdf_response !== null){
                        $cert_file_name = isset($pdf_response['cert_file_name']) ? $pdf_response['cert_file_name'] : null;
                        $cert_file_path = isset($pdf_response['cert_file_path']) ? $pdf_response['cert_file_path'] : null;
                        $cert_file_url = isset($pdf_response['cert_file_url']) ? $pdf_response['cert_file_url'] : null;
                        if($cert_file_name !== null){
                            $data['cert_file_name'] = $cert_file_name;
                        }
                        if($cert_file_path !== null){
                            $data['cert_file_path'] = $cert_file_path;
                        }
                        if($cert_file_url !== null){
                            $data['cert_file_url'] = $cert_file_url;
                            $download_certificate_html = "<div style='text-align:center;'>
                                <a target='_blank' href='" . $cert_file_url . "' class='action-button ays_download_certificate' download='" . $cert_file_name . "'>" . __( "Download your certificate", $this->plugin_name ) . "</a>
                            </div>";
                        }
                    }
                    
                    $data['quiz_end_date_timestamp'] = $quiz_total_time_for_user;
                    $data['user_id'] = $_REQUEST['user_id'];
                    $result = $this->add_results_to_db($data);
                    $g_last_id = $wpdb->insert_id;
                    $google_data['results_last_id'] = $_REQUEST['ays_quiz_result_row_id'];
                }else{
                    $result = true;
                }

                $last_result_id = $wpdb->insert_id;
                $last_result_id = $_REQUEST['ays_quiz_result_row_id'];


                $message_data['avg_score_by_category'] = Quiz_Maker_Data::ays_get_average_score_by_category($quiz_id);
                $message_data['download_certificate'] = $download_certificate_html;
                $interval_message = Quiz_Maker_Data::replace_message_variables($interval_message, $message_data);
                $message_data['interval_message'] = $interval_message;

                if($enable_send_mail_to_user_by_pass_score){
                    if($final_score < $pass_score_count){
                        $send_mail_to_user = false;
                    }
                }

                if ($send_mail_to_user) {
                    if (isset($_REQUEST['ays_user_email']) && filter_var($_REQUEST['ays_user_email'], FILTER_VALIDATE_EMAIL)) {
                        $message = (isset($options->mail_message)) ? $options->mail_message : '';
                        $message = Quiz_Maker_Data::replace_message_variables($message, $message_data);
                        $message = str_replace('%name%', stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ), $message);
                        $message = str_replace('%score%', $final_score, $message);
                        $message = str_replace('%logo%', $quiz_logo, $message);
                        $message = str_replace('%quiz_name%', stripslashes($quiz['title']), $message);
                        $message = str_replace('%date%', date("Y-m-d", current_time('timestamp')), $message);
                        $message = Quiz_Maker_Data::ays_autoembed( $message );
                        
                        if(! $force_mail_to_user){
                            if($send_interval_msg){
                                $message .= $interval_message;
                            }

                            // Send results to User
                            if ($send_results_user) {
                                $message_content = Quiz_Maker_Data::ays_report_mail_content($data, 'user', $send_results_user);
                                $message .= $message_content;
                            }
                        }
                        
                        $email = sanitize_email( $_REQUEST['ays_user_email'] );
                        $to = stripslashes( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) . " <$email>";

                        $headers = $nfrom."\r\n";
                        if($nreply != ""){
                            $headers .= $nreply."\r\n";
                        }
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $attachment = array();
                        if($cert && $final_score >= intval($options->certificate_pass)){
                            if($pdf_content === true){
                                $cert_path = $pdf_response['cert_path']; // array(__DIR__ . '/certificate.pdf');
                                $attachment = $cert_path;
                            }
                            $sendMail = false;
                            if($force_mail_to_user == true && $pdf_content === true){
                                $sendMail = true;
                            }elseif($force_mail_to_user == false){
                                $sendMail = true;
                            }
                            if($sendMail){
                                $ays_send_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
                            }
                        }elseif(!($cert && $final_score >= intval($options->certificate_pass))){
                            if(!$force_mail_to_user){
                                $ays_send_mail = (wp_mail($to, $subject, $message, $headers, $attachment)) ? true : false;
                            }
                        }
                    }
                }
                
                if($enable_send_mail_to_admin_by_pass_score){
                    if($final_score < $pass_score_count){
                        $send_mail_to_admin = false;
                    }
                }

                if ($send_mail_to_admin) {
                    if (filter_var(get_option('admin_email'), FILTER_VALIDATE_EMAIL)) {

                        $message_content = '';
                        $message_content = (isset($options->mail_message_admin) && $options->mail_message_admin != '') ? $options->mail_message_admin : '';
                        $message_content = Quiz_Maker_Data::replace_message_variables($message_content, $message_data);
                        $message_content = Quiz_Maker_Data::ays_autoembed( $message_content );

                        if($interval_message == ''){
                            $send_interval_msg_to_admin = false;
                        }
                        if($send_interval_msg_to_admin){
                            $message_content .= $interval_message;
                        }
                        if($send_results_admin){
                            $message_content .= Quiz_Maker_Data::ays_report_mail_content($data, 'admin', $send_results_admin);
                        }
                        if(!$send_interval_msg_to_admin && !$send_results_admin){
                            $message_content .= Quiz_Maker_Data::ays_report_mail_content($data, 'admin', null);
                        }

                        $admin_subject = ' - '.$data['user_name'].' - '.$data['score'].'%';
                        if($data['calc_method'] == 'by_points'){
                            $admin_subject = ' - '.$data['user_name'].' - '.$data['user_points'].'/'.$data['max_points'];
                        }
                        
                        if ($send_mail_to_site_admin) {
                            $admin_email = get_option('admin_email');
                            $email = "<$admin_email>";
                        }else{
                            $email = "";
                        }

                        $add_emails = "";
                        if(isset($options->additional_emails) && !empty($options->additional_emails)) {
                            if ($send_mail_to_site_admin) {
                                $add_emails = ", ";
                            }
                            $additional_emails = explode(", ", $options->additional_emails);
                            foreach($additional_emails as $key => $additional_email){
                                if($key==count($additional_emails)-1)
                                    $add_emails .= "<$additional_email>";
                                else
                                $add_emails .= "<$additional_email>, ";
                            }
                        }
                        $to = $email.$add_emails;

                        if(isset($options->use_subject_for_admin_email) && $options->use_subject_for_admin_email == "on") {

                        } else {
                            $subject = stripslashes($quiz['title']).$admin_subject;
                        }

                        $headers = $nfrom."\r\n";
                        if($nreply != ""){
                            $headers .= $nreply."\r\n";
                        }
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                        $attachment = array();

                        if($send_certificate_to_admin){
                            if($cert && $final_score >= intval($options->certificate_pass)){
                                if($pdf_content === true){
                                    $cert_path = $pdf_response['cert_path']; // array(__DIR__ . '/certificate.pdf');
                                    $attachment = $cert_path;
                                }
                                $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                            }elseif(!($cert && $final_score >= intval($options->certificate_pass))){
                                $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                            }
                        }else{
                            $ays_send_mail_to_admin = (wp_mail($to, $subject, $message_content, $headers, $attachment)) ? true : false;
                        }
                    }
                }
                
                if($enable_mailchimp && $mailchimp_list != ""){
                    if($mailchimp_username != "" && $mailchimp_api_key != ""){
                        $args = array(
                            "email" => $mailchimp_email,
                            "fname" => $mailchimp_fname,
                            "lname" => $mailchimp_lname,
                            "double_optin" => $enable_double_opt_in
                        );
                        $mresult = $this->ays_add_mailchimp_transaction($mailchimp_username, $mailchimp_api_key, $mailchimp_list, $args);
                    }
                }
                
            
                if ($enable_monitor && $monitor_list != "") {
                    if ($monitor_client != "" && $monitor_api_key != "") {
                        $args    = array(
                            "EmailAddress" => $monitor_email,
                            "Name"         => $monitor_name,
                        );
                        $mresult = $this->ays_add_monitor_transaction($monitor_client, $monitor_api_key, $monitor_list, $args);
                    }
                }
                
                if ($enable_active_camp) {
                    if ($active_camp_url != "" && $active_camp_api_key != "") {
                        $args    = array(
                            "email"     => $active_camp_email,
                            "firstName" => $active_camp_fname,
                            "lastName"  => $active_camp_lname,
                            "phone"     => $active_camp_phone,
                        );
                        $mresult = $this->ays_add_active_camp_transaction($active_camp_url, $active_camp_api_key, $args, $active_camp_list, $active_camp_automation);
                    }
                }
                
                if ($enable_zapier && $zapier_hook != "") {
                    if(! $zapier_flag){
                        $zresult = $this->ays_add_zapier_transaction($zapier_hook, $zapier_data);
                    }
                }
                
                if ($enable_slack && $slack_token != "") {
                    $sresult = $this->ays_add_slack_transaction($slack_token, $slack_conversation, $slack_data, $quiz['title'], $final_score);
                }
                
                if ($enable_google && $google_token != "") {
                    $sresult = $this->ays_add_google_sheets($google_data);
                }

                if( has_action( 'ays_qm_front_end_integrations' ) ){
                    $integration_args = array();
                    $integration_options = (array)$options;
                    $integration_options['id'] = $quiz_id;
                    $integrations_data = apply_filters('ays_qm_front_end_integrations_options', $integration_args, $integration_options);
                    do_action( "ays_qm_front_end_integrations", $integrations_data, $integration_options, $data );
                }

                if ($final_score >= $pass_score_count) {
                    $score_message = $pass_score_message;
                }else{
                    $score_message = $fail_score_message;
                }

                if($chained_quiz_id !== null ){
                    $chained_quiz_data = Quiz_Maker_Data::get_chained_quiz_by_id(intval($chained_quiz_id));

                    $chained_quiz_options = json_decode($chained_quiz_data['options']);
                    if( is_array( $chained_quiz_options ) ){
                        $chained_quiz_options = (object) $chained_quiz_options;
                    }

                    $print_report_table = isset($chained_quiz_options->chained_quizzes_print_report) && $chained_quiz_options->chained_quizzes_print_report == 'on' ? true : false;
                    $calculate_report_type = isset($chained_quiz_options->calculate_report_type) && $chained_quiz_options->calculate_report_type != '' ? $chained_quiz_options->calculate_report_type : 'take_quiz';

                    if($print_report_table){
                        if($calculate_report_type == 'pass_quiz'){
                            if($chained_quiz_see_result && ($final_score >= $pass_score_count)){
                                $chain_quiz_button_text = 'seeResult';
                            }else{
                                $chain_quiz_button_text = 'nextQuiz';
                            }
                        }else{
                            if($chained_quiz_see_result){
                                $chain_quiz_button_text = 'seeResult';
                            }else{
                                $chain_quiz_button_text = 'nextQuiz';
                            }
                        }
                    }else{
                        if($calculate_report_type == 'pass_quiz'){
                            if($chained_quiz_see_result && $final_score >= $pass_score_count){
                                $chain_quiz_button_text = '';
                            }else{
                                $chain_quiz_button_text = 'nextQuiz';
                            }
                        }else{
                            if(!$chained_quiz_see_result){
                                $chain_quiz_button_text = 'nextQuiz';
                            }else{
                                $chain_quiz_button_text = '';
                            }
                        }
                    }

                }

                $final_score_message = "";
                if($pass_score_count > 0){
                    $final_score_message = Quiz_Maker_Data::replace_message_variables($score_message, $message_data);
                }

                $finish_text = (isset($options->final_result_text) && $options->final_result_text != '') ? Quiz_Maker_Data::ays_autoembed( $options->final_result_text ) : '';
                $finish_text = Quiz_Maker_Data::replace_message_variables($finish_text, $message_data);
                
                $admin_mails = get_option('admin_email');
                $ays_send_mail_to_admin='';
                $ays_send_mail ='';
                $chain_quiz_button_text='';
                if ($result) {
                    $woo = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
                    $product = array();
                    if($product_id == ""){
                        $product_id = null;
                    }
                    if ($woo && isset($product_id)) {
                        $wpf = new WC_Product_Factory();
                        $cart_text = __('Add to cart', 'woocommerce');
                        $product_id = explode(',', $product_id);
                        foreach($product_id as $_key => $_value){
                            $product[] = array(
                                'prodUrl'  => get_permalink(intval($_value)),
                                'name'  => $wpf->get_product($_value)->get_data()['name'],
                                'image' => wp_get_attachment_image_src(get_post_thumbnail_id($_value), 'single-post-thumbnail')[0],
                                'link'  => "<a href=\"?add-to-cart=$_value\" data-quantity=\"1\" class=\"action-button product_type_simple add_to_cart_button ajax_add_to_cart\" data-product_id=\"$_value\" data-product_sku=\"\" aria-label=\"$cart_text\" rel=\"nofollow\">$cart_text</a>"
                            );
                        }
                    }
                
                    
                    ob_end_clean();
                    $ob_get_clean = ob_get_clean();

                    // echo json_encode(array(
                    //     "status" => true,
                    //     "cert_file_name" => isset( $data['cert_file_name'] ) ? $data['cert_file_name'] : null,
                    //     "hide_result" => false,
                    //     "showIntervalMessage" => $show_interval_message,
                    //     "score" => $score,
                    //     "scoreMessage" => $final_score_message,
                    //     "displayScore" => $display_score,
                    //     "finishText" => $finish_text,
                    //     "conditionData" => $conditions_data,
                    //     "product" => $product,
                    //     "intervalMessage" => $interval_message,
                    //     "mail" => $ays_send_mail,
                    //     "mail_to_admin" => $ays_send_mail_to_admin,
                    //     "admin_mail" => $admin_mails,
                    //     "result_id" => $last_result_id,
                    //     'interval_redirect_url' => $interval_redirect_url,
                    //     "interval_redirect_delay" => $interval_redirect_delay,
                    //     "interval_redirect_after" => $interval_redirect_after,
                    //     "chain_quiz_button_text" => $chain_quiz_button_text,
                    // ));
                    
                    $output = array('status' => true, 'error_code' => '0',
                        'message' => 'Test Finish Successfully',
                        'last_result_id'=>$last_result_id
                    );

                    // header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    // header('Content-type: application/json');
                    // header('Access-Control-Allow-Origin: *');

                    print json_encode($output);

                    die();
                }else{
                    ob_end_clean();
                    $ob_get_clean = ob_get_clean();
                    echo json_encode(array("status" => false, "message" => "No no no", "admin_mail" => $admin_mails ));
                    die();

                }

            } else {
                $admin_mails = get_option('admin_email');
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array("status" => false, "message" => "No no no", "admin_mail" => $admin_mails ));
                die();
            }
        }

    }
    }

    //forgot password

    public function forgot_password() {


        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        if (empty($params)) {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "Invalid input data"
            );
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($output);
            die();
        }

        $email = $params['email'];
        $newpassword = $params['user_newpassword'];
        $check_email_exist = email_exists($email);

        if (!$check_email_exist || $newpassword =='') {
            $output = array(
                "status" => false,
                "error_code" => "1109",
                "message" => "Account not exsist /may be email or phone not exsist "
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        } 
        else 
        {
            $get_user_details = get_user_by('email', $email);
            $login_details = $get_user_details->user_login;
            $email = $get_user_details->user_email;
            $user_id = $get_user_details->ID;

            $uniqueNo = rand();
            update_user_meta($user_id, 'user_password_hwe', $newpassword);
            update_user_meta($user_id, 'unknown_check', $uniqueNo);

            $message = '<p>Please click on this link to confirm your password '.get_site_url().'/forgot-password-message?emailLink='.base64_encode($email).'&unknownCheck='.$uniqueNo.'</p>';
            $title = 'Password Reset';
            
            $from = get_option('admin_email');

            $headers  = "From:".$from."\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            if (mail($email,$title,$message,$headers))
            {
                $output = array(
                    "status" => true,
                    "error_code" => "0",
                    "code" => $newpassword,
                    "message" => "Please check your email for password."
                );
            } else {

                $output = array(
                    "status" => false,
                    "error_code" => "1109",
                    "message" => "Password not sent on email."
                );
            }

            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
    }
    

    //user contect us api

    public function user_contect_us_api() {


        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;


        if (empty($params)) {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "Invalid input data"
            );
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            echo json_encode($output);
            die();
        }

        $user_name = $params['user_name'];
        $user_email=$params['user_email'];
        $user_mobile_no=$params['user_mobile_no'];
        $user_discreption=$params['user_description'];
       

        
            
            $message ='<p>'.$user_discreption.'<br>From: '.$user_name.'<br>Email: '.$user_email.'<br> Contact Number: '.$user_mobile_no.' </p>';
            $title = 'User Feedback Data';
            $email='info@testemoney.in';
            
            $from = get_option('admin_email');

            $headers  = "From:".$from."\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            if (mail($email,$title,$message,$headers))
            {
                $output = array(
                    "status" => true,
                    "error_code" => "0",
                    "message" => "Your Query Submitted Successfuly."
                  
                );
              
            } 
            else 
            {

                $output = array(
                    "status" => false,
                    "error_code" => "1109",
                    "message" => "Password not sent on email."
                );
                
            }

            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
        
    
//end user contect us api

    //admin banner lists////
    public function quiz_banner_list() {

        global $wpdb;
        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        $data = array();
        $all_data=array();
        //$banner_data = $wpdb->get_results("SELECT * from ".$wpdb->prefix."admin_banners",ARRAY_A);
        // $banner_data = array(
        //     0=>'https://secure.gravatar.com/avatar/67bcc577e39274e39e7fa53b9e29a571?s=96&d=mm&r=g',
        //     1=>'https://img.freepik.com/premium-vector/quiz-comic-pop-art-style_175838-505.jpg?w=2000',
        //     2=>'https://canopylab.com/wp-content/uploads/2020/05/Working-with-adaptive-quizzes-A-beginners-guide.jpg'
        // );

        $result_list=$wpdb->get_results("SELECT * FROM test_baner_image",ARRAY_A);
        
        
        if(count($result_list) > 0)
        {
            foreach($result_list as $banner_data_hwe)
            {
                // $data['banner'] = $banner_data_hwe['banner_image'];
              //  $data['banner'] = $banner_data_hwe;
           
                $banner_image_full_path=get_site_url()."/wp-content/uploads/banner_images/".$banner_data_hwe['image_name'];
                $all_data[]= $banner_image_full_path;
            }

            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Banners list",
                "data"=>$all_data
            );
        }
        else
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "No Banners list Found",
                "data"=>$all_data
            );
        }
   
        
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode($output);
        die();
  

    }
    /////end admin banner lists////


     //user terms And condition page////
     public function user_terms_condition_page() {

        global $wpdb;
        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

       
        $data=array();
        $all_data=array();
       
        $result_list=$wpdb->get_results("SELECT * FROM static_page_contant",ARRAY_A);
        
        
        if(count($result_list) > 0)
        {
            foreach($result_list as $banner_data_hwe)
            {
        
                 $title= json_decode($banner_data_hwe['teram_condition_title'],true);
                 $content= json_decode($banner_data_hwe['terms_condition_content'],true);
                 foreach($title as $key => $title_hwe)
                {   
                   $data['title']=$title_hwe;
                   $data['content']=$content[$key];

                   $all_data[]=$data;
                }
                
             
            }

            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Banners list",
                "data"=>$all_data
            );
        }
        else
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "No Banners list Found",
                "data"=>$all_data
            );
        }
   
        
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode($output);
        die();
  

    }
    /////end user terms And condition page////


         //about us page////
         public function user_about_us_page() {

            global $wpdb;
            $output = [];
            $this->maintaincemode();
            $this->db = $this->getDb();
            $headers = $this->getallheaders();
            $params = filter_input_array(INPUT_POST, $_POST);
            $params = $_REQUEST;
    
            $data=array();
            $all_data=array();
           
            $result_list=$wpdb->get_results("SELECT * FROM static_page_contant ",ARRAY_A);
            
            
            if(count($result_list) > 0)
            {
                foreach($result_list as $banner_data_hwe)
                {
                
                    $title= json_decode($banner_data_hwe['aboutus_title'],true);
                    $content= json_decode($banner_data_hwe['aboutus_content'],true);
                    foreach($title as $key => $title_hwe)
                    {   
                       $data['title']=$title_hwe;
                       $data['content']=$content[$key];
    
                       $all_data[]=$data;
                    }
                 
                }
    
                $output = array(
                    "status" => true,
                    "error_code" => "0",
                    "message" => "Banners list",
                    "data"=>$all_data
                );
            }
            else
            {
                $output = array(
                    "status" => false,
                    "error_code" => "1106",
                    "message" => "No Banners list Found",
                    "data"=>$all_data
                );
            }
       
            
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
    
            echo json_encode($output);
            die();
      
    
        }
        /////end about us page////

                 //Privacy Policy page////
                 public function privacy_policy_page() {

                    global $wpdb;
                    $output = [];
                    $this->maintaincemode();
                    $this->db = $this->getDb();
                    $headers = $this->getallheaders();
                    $params = filter_input_array(INPUT_POST, $_POST);
                    $params = $_REQUEST;
            
                   $data=array();
                   $all_data=array();
                   
                   
                    $result_list=$wpdb->get_results("SELECT * FROM static_page_contant ",ARRAY_A);
                    
                    
                    if(count($result_list) > 0)
                    {
                        foreach($result_list as $banner_data_hwe)
                        {
                            $title= json_decode($banner_data_hwe['privacypolicy_title'],true);
                            $content= json_decode($banner_data_hwe['privacypolicy_content'],true);
                            foreach($title as $key => $title_hwe){

                                $data['title']=$title_hwe;
                                $data['content']=$content[$key];

                                $all_data[]=$data;
                            }
                         
                        }
            
                        $output = array(
                            "status" => true,
                            "error_code" => "0",
                            "message" => "Banners list",
                             "data"=>$all_data
                        );
                    }
                    else
                    {
                        $output = array(
                            "status" => false,
                            "error_code" => "1106",
                            "message" => "No Banners list Found",
                            "data"=>$all_data
                        );
                    }
               
                    
                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
            
                    echo json_encode($output);
                    die();
              
            
                }
                /////end privacy policy page////


                         //contect us page////
         public function contect_us_page() {

            global $wpdb;
            $output = [];
            $this->maintaincemode();
            $this->db = $this->getDb();
            $headers = $this->getallheaders();
            $params = filter_input_array(INPUT_POST, $_POST);
            $params = $_REQUEST;
    
            $data=array();
            $all_data=array();
           
            $result_list=$wpdb->get_results("SELECT * FROM static_page_contant ",ARRAY_A);
            
            
            if(count($result_list) > 0)
            {
                foreach($result_list as $banner_data_hwe)
                {
                
                    $title= json_decode($banner_data_hwe['contectus_title'],true);
                    $content=json_decode($banner_data_hwe['contectus_content'],true);
                    foreach($title as $key => $title_hwe){

                        $data['title']=$title_hwe;
                        $data['content']=$content[$key];

                        $all_data[]=$data;
                    }
                 
                }
    
                $output = array(
                    "status" => true,
                    "error_code" => "0",
                    "message" => "Banners list",
                    "data"=>$all_data
                );
            }
            else
            {
                $output = array(
                    "status" => false,
                    "error_code" => "1106",
                    "message" => "No Banners list Found",
                    "data"=>$all_data
                );
            }
       
            
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
    
            echo json_encode($output);
            die();
      
    
        }
        /////end contect us page////

                   //ho to play page////
                   public function how_to_play_page() {

                    global $wpdb;
                    $output = [];
                    $this->maintaincemode();
                    $this->db = $this->getDb();
                    $headers = $this->getallheaders();
                    $params = filter_input_array(INPUT_POST, $_POST);
                    $params = $_REQUEST;
            
                    $data=array();
                    $all_data=array();
                   
                    $result_list=$wpdb->get_results("SELECT * FROM static_page_contant ",ARRAY_A);
                    
                    
                    if(count($result_list) > 0)
                    {
                        foreach($result_list as $banner_data_hwe)
                        {
                        
                            $title= json_decode($banner_data_hwe['howtoplay_title'],true);
                            $content=json_decode($banner_data_hwe['howtoplay_content'],true);
                            foreach($title as $key => $title_hwe){
        
                                $data['title']=$title_hwe;
                                $data['content']=$content[$key];
        
                                $all_data[]=$data;
                            }
                         
                        }
            
                        $output = array(
                            "status" => true,
                            "error_code" => "0",
                            "message" => "Banners list",
                            "data"=>$all_data
                        );
                    }
                    else
                    {
                        $output = array(
                            "status" => false,
                            "error_code" => "1106",
                            "message" => "No Banners list Found",
                            "data"=>$all_data
                        );
                    }
               
                    
                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
            
                    echo json_encode($output);
                    die();
              
            
                }
                /////end how to play page////

    // add balance to user wallet api
    public function user_add_balance() {

        global $wpdb;

        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;


        $output =array();

        $user_id = $params['user_id'];

        $amount=0;
        if(isset($params['amount']))
        {
            $amount = $params['amount'];
        }
        

        if(isset($params['user_id']) && $params['user_id'] != '' && $params['amount'] > 0)
        {

            $update_bal =get_user_meta( $user_id, 'bonuus_wallet');

            $total_bonuus_amount = $update_bal[0] + $amount;

            update_user_meta( $user_id, 'bonuus_wallet', abs( $total_bonuus_amount ) );

            $insert_transaction_data= $wpdb->prepare(
                "INSERT INTO ".$wpdb->prefix."mwb_wsfw_wallet_transaction SET `user_id`='$user_id',
                                            `amount`='$amount',
                                            `currency`='INR',
                                            `transaction_type`='Amount Added',
                                            `payment_method`='Payment By User',
                                            date =now()");
            $wpdb->query($insert_transaction_data);
        
            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Balance Added Successfully"
            );

            
        
            
        }
        else
        {
            if(!isset($params['user_id']) || $params['user_id'] != '')
            {
                $error = 'Amount should be greater then 0';
            }
            else
            {
                $error = 'Please try again';
            }

            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => $error
            );
        }
  
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
    }


    // withdraw wallet balance 
    public function user_withdraw_balance() {

        global $wpdb;

        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;
        $timestamp = current_time( 'timestamp' );
        $date = date("Y-m-d H:i:s",$timestamp);


        $output =array();

        

        $amount=0;
        if(isset($params['amount']))
        {
            $amount = abs($params['amount']);
        }
        $note='';
        if(isset($params['note']))
        {
            $note = $params['note'];
        }

        
        if(isset($params['user_id']) && $params['user_id'] != '' && $params['amount'] > 0)
        {
            $user_id = $params['user_id'];

            $get_balance =get_user_meta( $user_id, 'mwb_wallet');

            if($get_balance[0] >= $amount)
            {
                $total_bonuus_amount = abs($get_balance[0]) - $amount;

                if(update_user_meta( $user_id, 'mwb_wallet', abs( $total_bonuus_amount ) ))
                {

                    $insert_transaction_data= $wpdb->prepare(
                        "INSERT INTO ".$wpdb->prefix."mwb_wsfw_wallet_transaction SET `user_id`='$user_id',
                                                    `amount`='$amount',
                                                    `currency`='INR',
                                                    `transaction_type`='Balance Withdraw',
                                                    `payment_method`='Manually By Admin',
                                                    date =now()");
                    $wpdb->query($insert_transaction_data);

                    
                    $create_post = "INSERT into ".$wpdb->prefix."posts set post_author='$user_id',
                    post_date='$date',post_date_gmt='$date',post_content='',post_title='$date',post_excerpt='',post_status='pending1',comment_status='closed',
                    ping_status='closed',post_password='',post_name='$date',to_ping='',pinged='',post_modified='$date',post_modified_gmt='$date',post_content_filtered='',
                    post_parent='0',guid='',menu_order='',post_type='wallet_withdrawal',post_mime_type='',comment_count=''";

                    if($wpdb->query($create_post))
                    {
                        $lastid = $wpdb->insert_id;

                        update_post_meta($lastid,'mwb_wallet_withdrawal_amount',$amount);
                        update_post_meta($lastid,'mwb_wallet_note',$note);
                        update_post_meta($lastid,'wallet_user_id',$user_id);

                    }

                    $user = get_user_by( 'id', $user_id );
                    $username= $user->display_name;
                    $user_email= $user->user_email;

                    //////mail to admin//////
                    $to = 'info@testemoney.in';
                    $subject = 'User Balance Withdraw Request';
                    $from = get_option('admin_email');

                    $headers  = "From:".$from."\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                    $message = '<p>Balance <b>INR'.$amount.'</b> withdraw request sent by <strong>'.$username.'</strong></p>';

                    mail($to, $subject, $message, $headers);
                    //////end mail to admin//////

                    //////mail to user//////
                    $to1 = $user_email;
                    $subject1 = 'Balance Withdraw Request';
                    $message1 = '<p>Hii <strong>'.$username.'</strong>, Your withdraw request of <b>INR'.$amount.'</b> will be approved soon.</p>
                    <p>Thank You</p>';
                    mail($to1, $subject1, $message1, $headers);
                    //////end mail to user//////

                    $output = array(
                        "status" => true,
                        "error_code" => "0",
                        "message" => "Withdraw Balance Successfully"
                    );
                }

            }
            else
            {
                $output = array(
                    "status" => true,
                    "error_code" => "0",
                    "message" => "Insufficient Balance"
                );
            }

           
            
        }
        else
        {
            if(!isset($params['user_id']) || $params['user_id'] != '')
            {
                $error = 'Amount should be greater then 0';
            }
            else
            {
                $error = 'Please try again';
            }

            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => $error
            );
        }
  
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
    }


    // transfer wallet balance
    public function user_transfer_balance() {

        global $wpdb;

        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;


        $output =array();


        if($params['balance_transfer_email'] == '')
        {
            
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => 'Enter Email For Tranfer Balance'
            );
            
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $to_email = $params['balance_transfer_email'];

        

        $amount=0;
        if(isset($params['amount']))
        {
            $amount = $params['amount'];
        }

        
        if(isset($params['user_id']) && $params['user_id'] != '' && $params['amount'] > 0)
        {
            $user_id = $params['user_id'];

            $check_email_exist = email_exists($to_email);

            if (!$check_email_exist) {
                $output = array(
                    "status" => false,
                    "error_code" => "1109",
                    "message" => "User account not exsist"
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            } 
            else 
            {

                $get_user_details = get_user_by('email', $to_email);
         
                $user_id_to_send = $get_user_details->ID;
            

                $get_balance =get_user_meta( $user_id, 'mwb_wallet');
                $get_to_send_bonuus_balance = get_user_meta( $user_id_to_send, 'bonuus_wallet');

                if($get_balance[0] >= $amount)
                {
                    $total_bonuus_amount = $get_balance[0] - $amount;
                    update_user_meta( $user_id, 'mwb_wallet', abs( $total_bonuus_amount ) );

                    $total_to_send_bal = $get_to_send_bonuus_balance[0] + $amount;
                    update_user_meta( $user_id_to_send, 'bonuus_wallet', abs( $total_to_send_bal ) );

                    $insert_transaction_data= $wpdb->prepare(
                        "INSERT INTO ".$wpdb->prefix."mwb_wsfw_wallet_transaction SET `user_id`='$user_id',
                                                    `amount`='$amount',
                                                    `currency`='INR',
                                                    `transaction_type`='Balance Transfer',
                                                    `payment_method`='Transfer By User',
                                                    date =now()");
                    $wpdb->query($insert_transaction_data);
                
                    $output = array(
                        "status" => true,
                        "error_code" => "0",
                        "message" => "Withdraw Balance Successfully"
                    );
                }
                else
                {
                    $output = array(
                        "status" => false,
                        "error_code" => "0",
                        "message" => "Insufficient Balance"
                    );
                }

            }   

           
            
        }
        else
        {
            if(!isset($params['user_id']) || $params['user_id'] != '')
            {
                $error = 'Amount should be greater then 0';
            }
            else
            {
                $error = 'Please try again';
            }

            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => $error
            );
        }
  
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
    }

    //user wallet balance list show api
    public function user_wallet_balance_show() {

        global $wpdb;
        $output = [];
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        if($params['user_id'] == '')
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "Please again login"
            );
        
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            echo json_encode($output);
            die();
        }

        $user_id = $params['user_id'];

        $total_balance=0;
        $main_wallet =get_user_meta( $user_id, 'mwb_wallet',true);
        $bonuus_wallet =get_user_meta( $user_id, 'bonuus_wallet',true);
        $admin_bonuus_amount =get_user_meta( $user_id, 'admin_bonuus_amount',true);
        $total_balance = abs($main_wallet) + abs($bonuus_wallet) + abs($admin_bonuus_amount);
        $output = array(
            "status" => true,
            "error_code" => "0",
            "message" => "Wallet Balance",
            "total_balance"=>abs($total_balance),
            "withdrawal_wallet"=>abs($main_wallet),
            "bonuus_wallet"=>abs($bonuus_wallet),
            "admin_bonuus_wallet"=>abs($admin_bonuus_amount)
        );
        echo json_encode($output);
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        die();
  

    }

    //user Transaction list

    public function user_transactions_list() {

        global $wpdb;
        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        $start=0;
        $limit=10;

        $user_id = $params['user_id'];
        if(isset($params['start']) && $params['start'] !='')
        {
            $start = $params['start'];
        }

        if(isset($params['limit']) && $params['limit'] !='')
        {
            $limit = $params['limit'];
        }
        
    
    
        $select_transaction_data = $wpdb->get_results("SELECT * from ".$wpdb->prefix."mwb_wsfw_wallet_transaction where user_id='$user_id' order by id desc limit ".$start.",".$limit,ARRAY_A);

        
        $data = array();

        if(count($select_transaction_data) > 0)
        {

            $all_data =array();
            foreach($select_transaction_data as $value)
            {
                $data['amount'] = $value['amount'];
                $data['payment_method'] = $value['payment_method'];
                $data['details'] = $value['transaction_type'];
            // $data['transaction_id'] = $value['transaction_id'];
                $data['date'] = $value['date'];

                $all_data[] = $data;
            }

            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Transactions List",
                "results_list"=>$all_data
            );
        }
        else
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "No Results Found",
                "results_list"=>$data
            );
        }

        
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
  

    }

    // account verify api 
    public function user_account_details_verify() {

        global $wpdb;

        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        
        $output =array();
        $data =array();

        $error_code='1109';
        $message='Account details did not submit';
        $status= false;
  
        
        if(!isset($params['user_id']) || $params['user_id'] =='')
        {
            $message= 'Please again login';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['bank_name']) || $params['bank_name'] =='')
        {
            $message= 'Please enter bank name';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['branch_name']) || $params['branch_name'] =='')
        {
            $message= 'Please enter branch name';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['bank_account_number']) || $params['bank_account_number'] =='')
        {
            $message= 'Please enter account number';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['confirm_account_number']) || $params['confirm_account_number'] =='')
        {
            $message= 'Please enter confirm account number';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['ifsc_code']) || $params['ifsc_code'] =='')
        {
            $message= 'Please enter ifsc code';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['user_state']) || $params['user_state'] =='')
        {
            $message= 'Please enter state';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['user_email']) || $params['user_email'] =='')
        {
            $message= 'Please enter email';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['mobile_number']) || $params['mobile_number'] =='')
        {
            $message= 'Please enter mobile number';
            $error_code= '1109';
            $status= false;
        }
        elseif(!isset($params['pancard_number']) || $params['pancard_number'] =='')
        {
            $message= 'Please enter pancard number';
            $error_code= '1109';
            $status= false;
        } 
        elseif($params['bank_account_number'] != $params['confirm_account_number'])
        {
            $message= 'Account number and Confirm account number did not match';
            $error_code= '1109';
            $status= false;
        }
        else
        {


            $user_id = $params['user_id'];
            
            
                $data['account_holder_name'] = $params['account_holder_name'];
                $data['bank_name'] = $params['bank_name'];
                $data['branch_name'] = $params['branch_name'];
                $data['bank_account_number'] = $params['bank_account_number'];
            
                $data['ifsc_code'] = $params['ifsc_code'];
                $data['user_state'] = $params['user_state'];
                $data['user_email'] = $params['user_email'];
                $data['mobile_number'] = $params['mobile_number'];
                $data['pancard_number'] = $params['pancard_number'];
             

                foreach($data as $key => $data_hwe)
                {
                    update_user_meta($user_id,$key,$data_hwe);
                }


                $message= 'Account Details submitted Successfully';
                $error_code='0';
                $status= true;
            
           

        }

        

        $output = array(
            "status" => $status,
            "error_code" => $error_code,
            "message" => $message
        );
       
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
    }


    public function user_passbook_photo_upload() {

        global $wpdb;

        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_POST;

        
        $output =array();
        $data =array();

        $error_code='1109';
        $message='Account details did not submit';
        $status= false;
  
        
        if(!isset($params['user_id']) || $params['user_id'] =='')
        {
            $message= 'Please again login';
            $error_code= '1109';
            $status= false;
        }
        elseif((!isset($_FILES['passbook_photo']['name']) || $_FILES['passbook_photo']['name'] ==''))
        {
            $message= 'Please upload passbook photo';
            $error_code= '1109';
            $status= false;
        }
        else
        {
            $user_id = $params['user_id'];
            $passbook_photo_name  =$_FILES['passbook_photo']['name'];
            $passbook_photo_tmp_name  =$_FILES['passbook_photo']['tmp_name'];

            // $passbook_photo_name  =$params['passbook_photo_name'];
            // $passbook_photo_tmp_name  =$params['passbook_photo_url'];


            $current_dir = dirname(dirname(dirname(__FILE__)));
    
          
            if (!file_exists($current_dir.'/uploads/passbookimage')) {
                mkdir($current_dir.'/uploads/passbookimage', 0777, true);
            }
        //   echo $passbook_photo_tmp_name;
        //   echo "<br>";
        //   echo $current_dir.'/uploads/passbookimage/'.$passbook_photo_name;

        //   die("fdghdfg");
         
            if(move_uploaded_file($passbook_photo_tmp_name,$current_dir.'/uploads/passbookimage/'.$passbook_photo_name))
            {
                $image_url = get_site_url().'/wp-content/uploads/passbookimage/'.$passbook_photo_name;
            
                $data['passbook_photo'] = $image_url;

                foreach($data as $key => $data_hwe)
                {
                    update_user_meta($user_id,$key,$data_hwe);
                }

                $message= 'Account Details submitted Successfully';
                $error_code='0';
                $status= true;
            }
            else
            {
                $message= 'Passbook Image Did Not Upload';
                $error_code='1109';
                $status= false;
            }

        }
        $output = array(
            "status" => $status,
            "error_code" => $error_code,
            "message" => $message
        );
       
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
    }

   ////////////////////////normal graph marks///////////////

    public function quiz_marks_analytics() {

        global $wpdb;
        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        $start=0;
        $limit=10;

        $user_id = $params['user_id'];
  
      
        $cat_query='';
        if(isset($params['cat_id']) && $params['cat_id'] !='')
        {
            $category_id= $params['cat_id'];
            $cat_query = " && quiz_category_id='".$category_id."'";
        }

        if(isset($params['subcat_id']) && $params['subcat_id'] !='')
        {
            $sub_category_id= $params['subcat_id'];
            $subcat_query = " && quiz_subcategory_id='".$sub_category_id."'";
        }
        
        
       $select_query = "SELECT t1.id,t1.title,t1.quiz_category_id,t1.quiz_subcategory_id,t2.quiz_id,t2.user_id,t2.points,t2.id as report_id from ".$wpdb->prefix."aysquiz_quizes as t1 INNER JOIN ".$wpdb->prefix."aysquiz_reports as t2 ON t1.id=t2.quiz_id where 1='1'".$cat_query.$subcat_query." && user_id='".$user_id."' order by report_id desc limit 10";
        $get_results = $wpdb->get_results($select_query,ARRAY_A);
        $data =array();
        $all_data=array();

        if(count($get_results) > 0)
        {

            foreach($get_results as $value)
            {
                // $data['quiz_name'] = $value['title'];
                // $data['marks'] = abs($value['points']);

                // $all_data[] = $data;
                $data[] = $value['title'];
                $all_data[] = abs($value['points']);

            }

            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Results Found",
                "test_list"=>$data,
                "test_marks"=>$all_data
            );
        }
        else
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "No Results Found",
                "test_list"=>$data,
                "test_marks"=>$all_data
            );
        }

       
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
  

    }

    //end normal graph marks//

    ////////////////////////normal graph time///////////////

    public function quiz_time_analytics() {

        global $wpdb;
        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        $start=0;
        $limit=10;

        $user_id = $params['user_id'];
  
      
        $cat_query='';
        if(isset($params['cat_id']) && $params['cat_id'] !='')
        {
            $category_id= $params['cat_id'];
            $cat_query = " && quiz_category_id='".$category_id."'";
        }

        if(isset($params['subcat_id']) && $params['subcat_id'] !='')
        {
            $sub_category_id= $params['subcat_id'];
            $subcat_query = " && quiz_subcategory_id='".$sub_category_id."'";
        }
        
        
       $select_query = "SELECT t1.id,t1.title,t1.quiz_category_id,t1.quiz_subcategory_id,t2.quiz_id,t2.user_id,t2.current_timestamp,t2.id as report_id from ".$wpdb->prefix."aysquiz_quizes as t1 INNER JOIN ".$wpdb->prefix."aysquiz_reports as t2 ON t1.id=t2.quiz_id where 1='1'".$cat_query.$subcat_query." && user_id='".$user_id."' order by report_id desc limit 10";
        $get_results = $wpdb->get_results($select_query,ARRAY_A);
        $data =array();
        $all_data=array();

        if(count($get_results) > 0)
        {

            foreach($get_results as $value)
            {
                // $data['quiz_name'] = $value['title'];
                // $data['time'] = abs($value['current_timestamp']);

                // $all_data[] = $data;

                $data[] = $value['title'];
                // $all_data[] = abs($value['current_timestamp']);

                
                $hour='';
                if(date('h', $value['current_timestamp']) > 0)
                {
                    $hour = date('h', $value['current_timestamp']);
                    $hour = $hour.' hour ';
                }
                $min=0;
                if(date('i', $value['current_timestamp']) > 0)
                {
                    $min = date('i', $value['current_timestamp']);
                    $sec = $min.' min ';
                }
                $sec='';
                if(date('s', $value['current_timestamp']) > 0)
                {
                    $sec = date('s', $value['current_timestamp']);
                    $sec = '.'.$sec;
                }
                $all_data[] =$min.$sec;
               
                
            }

            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Results Found",
                "test_list"=>$data,
                "test_data"=>$all_data
            );
        }
        else
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "No Results Found",
                "test_list"=>$data,
                "test_data"=>$all_data
            );
        }

      
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
  

    }

    //end normal graph time//

    ////////////////////////comparision graph marks///////////////

    public function quiz_compare_marks_analytics() {

        global $wpdb;
        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        $start=0;
        $limit=10;

        $user_id = $params['user_id'];
  
      
        $cat_query='';
        if(isset($params['cat_id']) && $params['cat_id'] !='')
        {
            $category_id= $params['cat_id'];
            $cat_query = " && quiz_category_id='".$category_id."'";
        }

        if(isset($params['subcat_id']) && $params['subcat_id'] !='')
        {
            $sub_category_id= $params['subcat_id'];
            $subcat_query = " && quiz_subcategory_id='".$sub_category_id."'";
        }

        
        
        
        $select_query = "SELECT t1.id,t1.title,t1.quiz_category_id,t1.quiz_subcategory_id,t2.quiz_id,t2.user_id,t2.points,t2.id as report_id from ".$wpdb->prefix."aysquiz_quizes as t1 INNER JOIN ".$wpdb->prefix."aysquiz_reports as t2 ON t1.id=t2.quiz_id where 1='1'".$cat_query.$subcat_query." && user_id='".$user_id."' order by report_id desc limit 10";
        $get_results = $wpdb->get_results($select_query,ARRAY_A);
        $data =array();
        $all_data=array();
        $average_data =array();
        if(count($get_results) > 0)
        {

            foreach($get_results as $value)
            {
                $quiz_id = $value['id'];

                $select_query_average = "SELECT t3.quiz_id,t3.user_id,t3.points,t3.max_points,t3.id as report_id from ".$wpdb->prefix."aysquiz_reports as t3 where quiz_id='$quiz_id'";
                $get_results_average = $wpdb->get_results($select_query_average,ARRAY_A);

                $total_marks = 0;
                $average_marks=0;
                if(count($get_results_average) > 0)
                {
        
                    $count_users = count($get_results_average);
                    foreach($get_results_average as $value_average)
                    {
                        
                        $total_marks += abs($value_average['points']);
                        
                    }

                    $average_marks = $total_marks/$count_users;
        
                }

                
                $data[] = $value['title'];
                $all_data[] = abs($value['points']);
                $average_data[] = round($average_marks,2);
            }

            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Results Found",
                "test_list"=>$data,
                "test_data"=>$all_data,
                "average"=>$average_data
            );
        }
        else
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "No Results Found",
                "test_list"=>$data,
                "test_data"=>$all_data,
                "average"=>$average_data
            );
        }

       
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
  

    }

    //end comparision graph marks//

    ////////////////////////comparision graph time///////////////

    public function quiz_compare_time_analytics() {

        global $wpdb;
        $output = [];
        $this->maintaincemode();
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $params = $_REQUEST;

        $start=0;
        $limit=10;

        $user_id = $params['user_id'];
  
      
        $cat_query='';
        if(isset($params['cat_id']) && $params['cat_id'] !='')
        {
            $category_id= $params['cat_id'];
            $cat_query = " && quiz_category_id='".$category_id."'";
        }

        if(isset($params['subcat_id']) && $params['subcat_id'] !='')
        {
            $sub_category_id= $params['subcat_id'];
            $subcat_query = " && quiz_subcategory_id='".$sub_category_id."'";
        }

        $select_query = "SELECT t1.id,t1.title,t1.quiz_category_id,t1.quiz_subcategory_id,t1.active_create_date,t1.active_end_date,t2.quiz_id,t2.user_id,t2.current_timestamp,t2.id as report_id from ".$wpdb->prefix."aysquiz_quizes as t1 INNER JOIN ".$wpdb->prefix."aysquiz_reports as t2 ON t1.id=t2.quiz_id where 1='1'".$cat_query.$subcat_query." && user_id='".$user_id."' order by report_id desc limit 10";
        $get_results = $wpdb->get_results($select_query,ARRAY_A);
        $data =array();
        $all_data=array();
        $average_data = array();
        if(count($get_results) > 0)
        {

            foreach($get_results as $value)
            {
                $quiz_id = $value['id'];

                

                $select_query_average = "SELECT t3.quiz_id,t3.user_id,t3.current_timestamp,t3.max_points,t3.id as report_id from ".$wpdb->prefix."aysquiz_reports as t3 where quiz_id='$quiz_id'";
                $get_results_average = $wpdb->get_results($select_query_average,ARRAY_A);

         
                $average_time=0;
                if(count($get_results_average) > 0)
                {
        
                    $count_users = count($get_results_average);
                    foreach($get_results_average as $value_average)
                    {
                        
                        $total_time += abs($value_average['current_timestamp']);
                      
        
                        
                    }

                    $average_time = $total_time/$count_users;
        
                }

                $data[] = $value['title'];

                $hour='';
                if(date('h', $value['current_timestamp']) > 0)
                {
                    $hour = date('h', $value['current_timestamp']);
                    $hour = $hour.' hour ';
                }
                $min=0;
                if(date('i', $value['current_timestamp']) > 0)
                {
                    $min = date('i', $value['current_timestamp']);
                    $sec = $min.' min ';
                }
                $sec='';
                if(date('s', $value['current_timestamp']) > 0)
                {
                    $sec = date('s', $value['current_timestamp']);
                    $sec = '.'.$sec;
                }
                $all_data[] =$min.$sec;

              
           
                $average_data[] = date("i:s",round($average_time));

            }

            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "Results Found",
                "test_list"=>$data,
                "test_data"=>$all_data,
                "average"=>$average_data
            );
        }
        else
        {
            $output = array(
                "status" => false,
                "error_code" => "1106",
                "message" => "No Results Found",
                "test_list"=>$data,
                "test_data"=>$all_data,
                "average"=>$average_data
            );
        }
        
       
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($output);
        die();
  

    }

    //end comparision graph time//


    //login verify end
    //signup
    public function signup() {
    //error_log("\n=================== Signup ==================\n");
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $app_version = (isset($headers['appversion'])) ? $headers['appversion'] : '';
        $deviceid = (isset($headers['deviceid'])) ? $headers['deviceid'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';


        $nationality = $params['nationality'];
        $phone_number = $params['phone_number'];
        $email = $params['email'];
        $password = $params['password'];
        //$invitaion_code = $params['invitaion_code'];
        $captcha = $_POST['captcha'];

        $pincode = rand(1000, 5000);
        $accesstoken = substr(md5(time()), 0, 25);

        $invitation_code = '';

        //captcha code
        $captchahwe = $wpdb->get_results("SELECT captcha  FROM preLogin WHERE device_id = '$deviceid' order By id desc limit 0,1");
        if (count($captchahwe) == 0) {
            $captchahwe = $wpdb->get_results("SELECT captcha  FROM preLogin order By id desc limit 0,1");
        }

        $caphwe = $captchahwe[0]->captcha;

        if ($caphwe != $captcha || empty($captcha)) {
            $output = array('status' => false, 'error_code' => '2101',
                'message' => 'token invalid'
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        //check phone no 

        $sqlselect = $wpdb->get_results("select meta_value from ".$wpdb->prefix."usermeta where meta_key = 'phone_no' && meta_value = '$phone_number'");
        // print_r($sqlselect); exit;

        if (count($sqlselect) != 0) {
            $output = array('status' => false, 'error_code' => '1103',
                'message' => 'Phone number exist'
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        // Check the email address.
        if (empty($email) || !is_email($email)) {
            $output = array('status' => false, 'error_code' => '1112',
                'message' => 'Invalid input data'
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }


        if (email_exists($email)) {
//            error_log("\n=================== End Signup ==================\n");
            $output = array('status' => false, 'error_code' => '1102',
                'message' => 'Email exist'
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
        // Handle username creation.

        $username = sanitize_user(current(explode('@', $email)), true);
        // Ensure username is unique.
        $append = 1;
        $o_username = $username;
        while (username_exists($username)) {
            $username = $o_username . $append;
            $append++;
        }

        $languagecode = 'en-us';
        $notecode = urlencode("NoteCode.");
        $request_user_mobileNumber = $phone_number;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.nexmo.com/verify/json?api_key=6e45ad53&api_secret=MZ7MV9x0f7yqlkZU&number=" . $request_user_mobileNumber . "&brand=" . $notecode . "&code_length=6&lg=" . $languagecode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $decode = json_decode($response);
        // print_r($decode); exit;

        if (isset($decode->status)) {
            if ($decode->status == 0) {
                //insert query

                $title = 'Email Verification';
                $headers = 'Content-Type: text/html; charset=UTF-8';

                // using normal send email //
                $otp = mt_rand(100000, 999999);

                $table_name_hwe = $wpdb->prefix . "user_temp";
                $insert_query_hwe = "INSERT INTO $table_name_hwe SET nationality='$nationality', phone_number='$phone_number', email='$email', username='$username', password='$password', captcha='$caphwe', app_version='$app_version', email_code='$otp', email_code_validity = " . (time() + 3600) . ", isEmailVerfied = 'false' , isPhoneVerified = 'false', is2FAEnabled = 'false'";
                $insert_hwe = $wpdb->query($insert_query_hwe);
                $lastInsertId = $this->db->insert_id;

                $sendEmailDatas = array(
                    'username' => $username,
                    'otp' => $otp,
                    'date' => date('Y-m-d H:i:00', strtotime('+5 minutes'))
                );
                $sendEmail = $this->sendEmail('signup', $email, $sendEmailDatas);
                if ($sendEmail === false) {
                    //error_log("unable to send email on release_coin order: $order_id");
                }


                // using smpt.gmail //
                /*
                  $path=WP_CONTENT_DIR."/"."plugins/coinsapi/PHPMailer-FE_v4.11/_lib/class.phpmailer.php";

                  require_once("$path");
                  //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

                  $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

                  $mail->IsSMTP(); // telling the class to use SMTP



                  try {
                  $mail->Host       = "smtp.gmail.com"; // SMTP server
                  $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
                  $mail->SMTPAuth   = true;                  // enable SMTP authentication
                  $mail->Host       = "smtp.gmail.com"; // sets the SMTP server
                  $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
                  $mail->Username   = "noreply@idospa.com"; // SMTP account username
                  $mail->Password   = "UyKL4b^SJZEf_Kxd";        // SMTP account password
                  $mail->AddReplyTo('rahua567@gmail.com', 'First Last');
                  $mail->AddAddress('rahua567@gmail.com', 'John Doe');
                  $mail->SetFrom('rahua567@gmail.com', 'First Last');
                  $mail->AddReplyTo('rahua567@gmail.com', 'First Last');
                  $mail->Subject = 'PHPMailer Test Subject via mail(), advanced';
                  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
                  $mail->MsgHTML(file_get_contents('contents.html'));
                  $mail->AddAttachment('images/phpmailer.gif');      // attachment
                  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
                  $mail->Send();
                  echo "Message Sent OK\n";
                  } catch (phpmailerException $e) {
                  echo $e->errorMessage(); //Pretty error messages from PHPMailer
                  } catch (Exception $e) {
                  echo $e->getMessage(); //Boring error messages from anything else!
                  }
                 */

                $request_id = $decode->request_id;

                $success = array('status' => true, 'error_code' => '0',
                    'message' => "user succesfully registerd"
                );

                $userdata = array('data' => array("request_id" => $request_id,
                        "request_register_id" => $lastInsertId,
                    //"query" => $insert_query_hwe
                    )
                );
                $output = array_merge($success, $userdata);

//        error_log("\nresponse: " . json_encode($output) . "\n");
//        error_log("\n=================== End Signup ==================\n");

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            } else {
                $output = array('status' => false, 'error_code' => '10000',
                    'message' => "Please used another phone number or waiting for another 5 minutes"
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            }
        } else {
            $output = array('status' => false, 'error_code' => '10000',
                'message' => "Please used another phone number or waiting for another 5 minutes"
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
     
    }

// signup end
    //signup_verify

    public function signup_verify() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = $decode = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);


        $request_register_id = $params['request_register_id'];
        $pincode = $params['pinCode'];
        $request_id = $params['request_id'];

        $app_version = (isset($headers['appversion'])) ? $headers['appversion'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        error_log("signup verify: " . json_encode($_POST));

        $pin_exipry = 150;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.nexmo.com/verify/check/json?api_key=6e45ad53&api_secret=MZ7MV9x0f7yqlkZU&request_id=" . $request_id . "&code=" . $pincode . "&pin_exipry=" . $pin_exipry,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $decode = json_decode($response);
        error_log("signup verify: $pincode" . $response);

        //$decode->status = true;

        if (isset($decode->status)) {
            if ($decode->status == 0) {
                $table_name_hwe = $wpdb->prefix . "user_temp";
                $sqlselect = $wpdb->get_results("SELECT * FROM $table_name_hwe WHERE id = '$request_register_id'");
                if (count($sqlselect) != 0) {
                    $updateUserTmp = $wpdb->get_results("UPDATE $table_name_hwe SET isPhoneVerified='true' WHERE id = '$request_register_id'");
                    $output = array('status' => true, 'error_code' => '0',
                        'message' => "Mobile verification is done"
                    );
                } else {
                    $output = array('status' => false, 'error_code' => '1101',
                        'message' => "No user found!"
                    );
                }

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            } else {
                $output = array('status' => false, 'error_code' => '1112',
                    'message' => "Invalid input data"
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            }
        } else {
            $output = array('status' => false, 'error_code' => '1112',
                'message' => "Invalid input data"
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
    }

//end verfiy

    public function email_verify() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = $newAddress = [];
        $addressError = 0;
        $this->db = $this->getDb();
        $params = filter_input_array(INPUT_POST, $_POST);

        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $otp_code = $params['pinCode'];
        $request_register_id = $params['request_register_id'];

        $table_name_hwe = $wpdb->prefix . "user_temp";
        $myrows = "SELECT * FROM $table_name_hwe where id = '$request_register_id'";
        $get_result_hwe = $wpdb->get_results($myrows);

        $email_code = $get_result_hwe[0]->email_code;
        $phoneVerify = $get_result_hwe[0]->isPhoneVerified;
        $email_code_validity = $get_result_hwe[0]->email_code_validity;

        if ($phoneVerify === false || $phoneVerify == 'false' || $phoneVerify != true) {
            $output = array('status' => false, 'error_code' => '1104',
                'message' => 'Tac code not sent /even email or phone anyone code not send'
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if (time() >= $email_code_validity) {
            $output = array('status' => false, 'error_code' => '1107',
                'message' => 'TAC expired'
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($email_code == $otp_code) {
            $sqlselect = $wpdb->get_results("SELECT * FROM $table_name_hwe WHERE id = '$request_register_id'");
            if (count($sqlselect) != 0) {
                $updateUserTmp = $wpdb->get_results("UPDATE $table_name_hwe SET isEmailVerfied='true' WHERE id = '$request_register_id'");
                $output = array('status' => true, 'error_code' => '0',
                    'message' => "Mobile verification is done"
                );
            } else {
                $output = array('status' => false, 'error_code' => '1112',
                    'message' => "Invalid input data"
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            }

            $accesstoken = substr(md5(time()), 0, 25);

            //$get_result_hwe[0]->id;
            $nationality = $get_result_hwe[0]->nationality;
            $phone_number = $get_result_hwe[0]->phone_number;
            $username = $get_result_hwe[0]->username;
            $password = $get_result_hwe[0]->password;
            $email = $get_result_hwe[0]->email;
            $captcha = $get_result_hwe[0]->captcha;
            $app_version = $get_result_hwe[0]->app_version;
            $emailtoken = $get_result_hwe[0]->email_code;


            $userdata = array('user_login' => $username,
                'user_pass' => $password,
                'user_email' => $email
            );

            $user_id = wp_insert_user($userdata);
            if (isset($user_id->errors)) {
                $output = array('status' => false, 'error_code' => '1112',
                    'message' => 'Invalid input data'
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            }

            update_user_meta($user_id, 'email_token', $emailtoken);
            update_user_meta($user_id, 'user_accesstoken', $accesstoken);
            update_user_meta($user_id, 'user_nationality', $nationality);
            update_user_meta($user_id, 'phone_no', $phone_number);
            update_user_meta($user_id, 'user_captcha', $captcha);
            update_user_meta($user_id, 'user_app_version', $app_version);
            update_user_meta($user_id, 'isEmailVerfied', 'true');
            update_user_meta($user_id, 'isPhoneVerified', 'true');
            update_user_meta($user_id, 'is2FAEnabled', 'false');
            update_user_meta($user_id, 'isIdentityVerfied', 'false');
            update_user_meta($user_id, 'isTradePasswordSet', 'false');
            update_user_meta($user_id, 'google_add_code', mt_rand(100000, 999999));
            update_user_meta($user_id, 'isNickname', 'false');

            $encrypt_method = "AES-256-CBC";
            $key = hash('sha256', AUTH_SALT);
            $iv = substr(hash('sha256', SECRET_IV), 0, 16);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_BTC . "&newtwork=BTC&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $btc_curl = curl_exec($curl);
            error_log("btc: " . json_encode($btc_curl));
            $btc_checkingAddress = json_decode($btc_curl);

            if (strtolower($btc_checkingAddress->status) === 'success') {
                $btc_encryptedprivateKey = base64_encode(openssl_encrypt($btc_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                update_user_meta($user_id, 'btc_address', $btc_encryptedprivateKey);
                update_user_meta($user_id, 'btc_address_duplicate', $btc_checkingAddress->data->address);
            }


            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_JE . "&newtwork=JE&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $je_curl = curl_exec($curl);
            error_log("je: " . json_encode($je_curl));
            $je_checkingAddress = json_decode($je_curl);

            if (strtolower($je_checkingAddress->status) == "success") {
                $je_encryptedprivateKey = base64_encode(openssl_encrypt($je_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                update_user_meta($user_id, 'je_address', $je_encryptedprivateKey);
                update_user_meta($user_id, 'je_address_duplicate', $je_checkingAddress->data->address);
            }


            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_USDT . "&newtwork=USDT&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $usdt_curl = curl_exec($curl);
            error_log("usdt: " . json_encode($usdt_curl));
            $usdt_checkingAddress = json_decode($usdt_curl);

            if (strtolower($usdt_checkingAddress->status) === 'success') {
                $usdt_encryptedprivateKey = base64_encode(openssl_encrypt($usdt_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                update_user_meta($user_id, 'usdt_address', $usdt_encryptedprivateKey);
                update_user_meta($user_id, 'usdt_address_duplicate', $usdt_checkingAddress->data->address);
            }

            curl_close($curl);

            $user = get_user_by('email', $email);
            $isMerchant = (implode('', $user->roles) == 'merchant') ? true : false;
            $isPhone = get_user_meta($user_id, 'isPhoneVerified', true);
            $isEmail = get_user_meta($user_id, 'isEmailVerfied', true);
            $isTradePasswordSet = get_user_meta($user_id, 'isTradePasswordSet', true);
            $is2FAEnabled = get_user_meta($user_id, 'is2FAEnabled', true);
            switch (get_user_meta($merchant_id, 'isIdentityVerfied', true)) {
                case 'true':
                    $isIdentify = true;
                    break;
                case 'pending':
                    $isIdentify = 'pending';
                    break;
                case 'false':
                    $isIdentify = false;
                    break;
                default:
                    $isIdentify = false;
                    break;
            }

            $sendEmailDatas = array(
                'username' => $username
            );
            $sendEmail = $this->sendEmail('success_register', $email, $sendEmailDatas);
            if ($sendEmail === false) {
                //error_log("unable to send email on release_coin order: $order_id");
            }

            $success = array('status' => true, 'error_code' => '0',
                'message' => "User succesfully Signed In"
            );

            $userdata = array('data' => array(
                    "user_id" => $user_id,
                    "uat" => $accesstoken,
                    "email" => $email,
                    "userName" => $username,
                    'isMerchant' => $isMerchant,
                    'isPhoneVerified' => ($isPhone != '') ? ($isPhone == 'true') ? true : false : false,
                    'isEmailVerified' => ($isEmail != '') ? ($isEmail == 'true') ? true : false : false,
                    'is2FAEnabled' => ($is2FAEnabled != '') ? ($is2FAEnabled == 'true') ? true : false : false,
                    "isIdentityVerfied" => $isIdentify,
                    "isTradePasswordSet" => ($isTradePasswordSet != '') ? ($isTradePasswordSet == 'true') ? true : false : false
                )
            );

            $output = array_merge($success, $userdata);

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        } else {
            $output = array('status' => false, 'error_code' => '1112',
                'message' => 'Invalid input data',
                'error' => "$email_code == $otp_code"
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
    }

//fucntion ends here
   
    public function retrieve_password($user_login) {
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        if (empty($user_login)) {
            return false;
        } else {
            $login = trim($user_login);
            $user_data = get_user_by('login', $login);
        }
        do_action('lostpassword_post');
        if (!$user_data)
            return false;
        // redefining user_login ensures we return the right case in the email
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        do_action('retreive_password', $user_login);  // Misspelled and deprecated
        do_action('retrieve_password', $user_login);
        $allow = apply_filters('allow_password_reset', true, $user_data->ID);
        if (!$allow)
            return false;
        else if (is_wp_error($allow))
            return false;

        $key = wp_generate_password(20, false);
        do_action('retrieve_password_key', $user_login, $key);

        if (empty($wp_hasher)) {
            require_once ABSPATH . 'wp-includes/class-phpass.php';
            $wp_hasher = new PasswordHash(8, true);
        }
        $usertbb = $this->db->prefix . "users";
        $hashed = time() . ':' . $wp_hasher->HashPassword($key);
        $this->db->update($usertbb, array('user_activation_key' => $hashed), array('user_login' => $user_login));
        return network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
    }

    //refresh token api

    public function refresh_token() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $app_version = (isset($headers['appversion'])) ? $headers['appversion'] : '';
        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = $params['user_id'];

        $uatdata = get_user_meta($user_id, 'user_accesstoken', true);
        if ($uatdata == $uat) {
            $accesstoken = substr(md5(time()), 0, 25);
            update_user_meta($user_id, 'user_accesstoken', $accesstoken);
            $output = array(
                "status" => true,
                "error_code" => "0",
                "message" => "UAT not matched",
                "data" => array('uat' => $accesstoken)
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        } else {

            $output = array('status' => false, "error_code" => "2102",
                'message' => "multiple login detected"
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
    }

//function end here
    //change password 
    public function change_password() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $app_version = (isset($headers['appversion'])) ? $headers['appversion'] : '';
        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $old_pass = $params['old_pass'];
        $new_pass = $params['new_pass'];
        $info = get_userdata($user_id);
        $user = get_user_by('id', $user_id);
        // print_r($info);

        $password = $info->user_pass;

        if ($old_pass === $new_pass) {
            $output = array('status' => false, 'error_code' => '1111',
                'message' => "Invalid password"
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        $uatdata = get_user_meta($user_id, 'user_accesstoken', true);
        if ($uatdata == $uat) {

            if ($user && wp_check_password($old_pass, $password, $user_id)) {
                wp_set_password($new_pass, $user_id);
                update_user_meta($userID, 'disabled_withdraw', time());

                $output = array('status' => true, 'error_code' => '0',
                    'message' => 'Password Changed Successfully'
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            } else {

                $output = array('status' => false, 'error_code' => '1110',
                    'message' => "Old password incorrect"
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            }
        } else {

            $output = array('status' => false, "error_code" => "2102",
                'message' => "multiple login detected"
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }
    }

//function ends here
    //prelogin api

    public function requestcaptcha() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);


        $device_id = (isset($headers['deviceid'])) ? $headers['deviceid'] : '';
        $session = (isset($headers['session'])) ? $headers['session'] : '';
        $is_mobile = (isset($headers['is_mobile'])) ? $headers['is_mobile'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $created = time();
        $updated = time();

        $sesshwe = $session + 1;
        $captcha = substr(md5(time()), 0, 6);
        $wpdb->insert('preLogin', array('device_id' => $device_id, 'captcha' => $captcha, 'is_mobile' => $is_mobile, 'created' => $created, 'updated' => $updated));

        $success = array("status" => true,
            "error_code" => "0",
            "message" => "Captcha successfully"
        );
        $data = array('data' => array("capctha" => $captcha,
                "session" => 2
            )
        );
        $output = array_merge($success, $data);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        print json_encode($output);
        die();
    }

//function ends here
    //sceret pin change api
    public function set_trade_pin() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);


        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $app_version = (isset($headers['appversion'])) ? $headers['appversion'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $pinCode = (isset($params['pinCode'])) ? $params['pinCode'] : '';
        $user_id = $_POST['user_id'];
        $old_pin = (isset($params['old_pin'])) ? $params['old_pin'] : '';
        $new_pin = $params['new_pin'];

        $users = get_user_by('ID', $user_id);
        $old_sceretpin = get_user_meta($user_id, 'duplicate_secret_pin', true);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        $isTradePasswordSet = get_user_meta($user_id, 'isTradePasswordSet', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($old_sceretpin != '') {
            $table_name_hwe = $wpdb->prefix . "user_temp";
            $sqlselect = $wpdb->get_results("SELECT * FROM $table_name_hwe WHERE email_code = '$pinCode'");
            if (count($sqlselect) == 0) {
                $output = array('status' => false, 'error_code' => '1105',
                    'message' => 'TAC mismatch'
                );
                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            }

            if ($isTradePasswordSet != '' && $isTradePasswordSet === 'true') {
                if ($old_pin == '' || $old_pin == null) {
                    $output = array('status' => false, 'error_code' => '1110',
                        'message' => 'Old password incorrect'
                    );

                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
                    print json_encode($output);
                    die();
                }

                if ($old_sceretpin == $old_pin) {
                    update_user_meta($user_id, 'duplicate_secret_pin', $new_pin);
                    $hashed_pin = password_hash($new_pin, PASSWORD_DEFAULT);
                    update_user_meta($user_id, 'secret_pin', $hashed_pin);
                    update_user_meta($user_id, 'disabled_withdraw', time());

                    $output = array('status' => true, 'error_code' => '0',
                        'message' => "Reset Trading pin Changed successfully"
                    );

                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
                    print json_encode($output);
                    die();
                } else {
                    $output = array('status' => false, 'error_code' => '1110',
                        'message' => 'Old password incorrect'
                    );

                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
                    print json_encode($output);
                    die();
                }
            } else {
                $output = array('status' => false, 'error_code' => '1106',
                    'message' => 'blacklisted user / problems with account'
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            }
        } else {

            $table_name_hwe = $wpdb->prefix . "user_temp";
            $sqlselect = $wpdb->get_results("SELECT * FROM $table_name_hwe WHERE email_code = '$pinCode'");
            if (count($sqlselect) == 0) {
                $output = array('status' => false, 'error_code' => '1105',
                    'message' => 'TAC mismatch'
                );

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            }

            update_user_meta($user_id, 'duplicate_secret_pin', $new_pin);
            $hashed_pin = password_hash($new_pin, PASSWORD_DEFAULT);

            update_user_meta($user_id, 'secret_pin', $hashed_pin);
            update_user_meta($user_id, 'isTradePasswordSet', 'true');

            $output = array('status' => true, 'error_code' => '0',
                'message' => "Trading pin set successfully"
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
    }

//func ends here 
    //google authentication
    public function google_authentication() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $usertoken = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        //$enabled = (isset($params['enable'])) ? $params['enable'] : '';

        $user_info = get_userdata($user_id);
        $email_account = $user_info->user_email . "\n";
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        $isGoogleAuth = get_user_meta($user_id, 'is2FAEnabled', true);

        if ($usertoken != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($isGoogleAuth == 'true' || $isGoogleAuth === true) {
            update_user_meta($user_id, 'is2FAEnabled', 'false');
            $output = array(
                'status' => true,
                'error_code' => '0',
                'message' => "Google Authentication already set",
                'data' => array(
                    'is2FAEnabled' => $isGoogleAuth
                )
            );
        } else {
            //google authentication
            require_once 'GoogleAuthenticator.php';
            $account = $email_account;
            //echo "Account: $account \n\n";

            $ga = new PHPGangsta_GoogleAuthenticator();
            $privateKey = $ga->createSecret();
            //echo "private key is: ".$privateKey."\n\n";
            //$qrCodeUrl = $ga->getQRCodeGoogleUrl($account, $privateKey);
            $qrCodeUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=otpauth%3A%2F%2Ftotp%2F" . $email_account . "%3Fsecret%3D" . $privateKey . "&choe=json";
            //echo "Google Charts URL for the QR-Code: \n".$qrCodeUrl."\n\n";

            $publicKey = $ga->getCode($privateKey);
            //echo "public key: '$publicKey' and private key: '$privateKey':\n\n";
            //encrption
            $encrypt_method = "AES-256-CBC";
            $key = hash('sha256', AUTH_SALT);
            $iv = substr(hash('sha256', SECRET_IV), 0, 16);
            $encryptedprivateKey = base64_encode(openssl_encrypt($privateKey, $encrypt_method, $key, 0, $iv));

            $table_name_hwe = $wpdb->prefix . "user_temp";
            $updateUserTmp = $wpdb->get_results("UPDATE $table_name_hwe SET is2FAEnabled='true' WHERE email LIKE '%$email_account%'");

            $output = array(
                'status' => true,
                'error_code' => '0',
                'message' => "Google Authentication set",
                'data' => array(
                    'qrCodeUrl' => $qrCodeUrl,
                    'privateKey' => $privateKey,
                    'google_code' => $encryptedprivateKey
                )
            );
        }

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

//func ends here
    //google verify 

    public function google_verify() {
        //$this->app_version();
        include('GoogleAuthenticator.php');
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $usertoken = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $opt_code = $params['pinCode'];
        $google_code = (isset($params['google_code'])) ? $params['google_code'] : '';
        // print_r($params);

        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        if ($usertoken != $tokens) {
            $googleCodes = get_user_meta($user_id, 'encryptedprivateKey', true);
            if ($google_code != $googleCodes) {
                $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            }
        }

        $private_key = get_user_meta($user_id, 'encryptedprivateKey', true);

        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', AUTH_SALT);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);
        $decryptedprivateKey = openssl_decrypt(base64_decode($private_key), $encrypt_method, $key, 0, $iv);

//error_log('private: ' . json_encode($decryptedprivateKey));        
//error_log('code insert: ' . json_encode($opt_code));


        $ga = new PHPGangsta_GoogleAuthenticator();
        $checkResult = $ga->verifyCode($decryptedprivateKey, $opt_code);
//error_log('G2FA: ' . json_encode($checkResult));
        if ($checkResult) {
            $user = get_userdata($user_id);

            $accesstoken = substr(md5(time()), 0, 25);
            $email = $user->data->user_email;
            $username = $user->data->user_nicename;
            $request_id = $decode->request_id;

            // update_user_meta($user_id, 'user_accesstoken', $accesstoken);
            update_user_meta($user_id, 'is2FAEnabled', 'true');

            $output = array('status' => true, 'error_code' => '0', 'message' => 'OTP succefully');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        } else {
            $output = array('status' => false, 'error_code' => '1105', 'message' => 'TAC mismatch');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
    }

    public function google_authentication_status() {
        //$this->app_version();
        include('GoogleAuthenticator.php');
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $usertoken = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $opt_code = $params['pinCode'];
        $google_code = (isset($params['google_code'])) ? $params['google_code'] : '';
        $googleStatus = (isset($params['enabled'])) ? ($params['enabled'] != '' && $params['enabled'] != null) ? $params['enabled'] : 'true' : 'trues';
        $google_add_code = get_user_meta($user_id, 'google_add_code', true);
        // print_r($params);

        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        if ($usertoken != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $user_info = get_userdata($user_id);
        $email = (isset($user_info->data->user_email)) ? $user_info->data->user_email : '';
        $checkGoogleAuth = get_user_meta($user_id, 'is2FAEnabled', true);

        if ($checkGoogleAuth == 'false' || $checkGoogleAuth == false || $checkGoogleAuth == '') {
            if ($google_code == '' || $google_code == $google_add_code) {
                $output = array('status' => false, 'error_code' => '1105', 'message' => 'TAC mismatch');

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            }
            $encrypt_method = "AES-256-CBC";
            $key = hash('sha256', AUTH_SALT);
            $iv = substr(hash('sha256', SECRET_IV), 0, 16);
            $decryptedprivateKey = openssl_decrypt(base64_decode($google_code), $encrypt_method, $key, 0, $iv);
        } else {
            $private_key = get_user_meta($user_id, 'encryptedprivateKey', true);
            $encrypt_method = "AES-256-CBC";
            $key = hash('sha256', AUTH_SALT);
            $iv = substr(hash('sha256', SECRET_IV), 0, 16);
            $decryptedprivateKey = openssl_decrypt(base64_decode($private_key), $encrypt_method, $key, 0, $iv);
        }


        $ga = new PHPGangsta_GoogleAuthenticator();
        $checkResult = $ga->verifyCode($decryptedprivateKey, $opt_code);


        if ($checkResult) {
            $qrCodeUrl = $ga->getQRCodeGoogleUrl($email, $decryptedprivateKey);
            $user = get_userdata($user_id);
            $email = $user->data->user_email;
            $username = $user->data->user_nicename;
            $request_id = $decode->request_id;

            if ($checkGoogleAuth == 'false' || $checkGoogleAuth == false || $checkGoogleAuth == '') {
                $output = array('status' => true, 'error_code' => '0', 'message' => 'Google Authentication actived!');

                update_user_meta($user_id, 'qrCodeUrl', $qrCodeUrl);
                update_user_meta($user_id, 'encryptedprivateKey', $google_code);
                update_user_meta($user_id, 'is2FAEnabled', 'true');
                update_user_meta($user_id, 'disabled_withdraw', time());
            } else {
                if ($googleStatus == 'true' || $googleStatus === true) {
                    $output = array('status' => true, 'error_code' => '0', 'message' => 'OTP succefully');
                    update_user_meta($user_id, 'is2FAEnabled', 'true');
                } else {

                    $output = array('status' => true, 'error_code' => '0', 'message' => 'Google Authentication disabled!');
                    update_user_meta($user_id, 'qrCodeUrl', '');
                    update_user_meta($user_id, 'encryptedprivateKey', '');
                    update_user_meta($user_id, 'is2FAEnabled', 'false');
                }
            }

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        } else {

            $output = array('status' => false, 'error_code' => '1105', 'message' => 'TAC mismatch');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
    }

    public function resendEmailCode() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $creds = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $request_register_id = (isset($params['request_register_id'])) ? $params['request_register_id'] : '';
        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        $title = 'Email Verification';
        $headers = 'Content-Type: text/html; charset=UTF-8';
        $otp = mt_rand(100000, 999999);
        $str = "Email Verification OTP Code: ";
        $message = $str . $otp;

        if ($request_register_id != '') {

            $table_name_hwe = $wpdb->prefix . "user_temp";
            $updated_query_hwe = "UPDATE $table_name_hwe SET email_code='$otp', email_code_validity = " . strtotime('+5 minutes') . " WHERE id = $request_register_id";
            $wpdb->query($updated_query_hwe);

            $sqlEmail = $this->db->get_results("SELECT email FROM $table_name_hwe WHERE id = $request_register_id");
            $email = (count($sqlEmail) != 0) ? $sqlEmail[0]->email : '';
        } else if ($user_id != '') {

            if ($uat == '') {
                $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            }

            if ($uat != $tokens) {
                $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            }
            update_user_meta($user_id, 'email_code', $otp);
            $user_info = get_userdata($user_id);
            $email = (isset($user_info->data->user_email)) ? $user_info->data->user_email : '';
        } else {

            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        error_log('send email: ' . json_encode($email));
        error_log('send email: ' . json_encode($title));
        error_log('send email: ' . json_encode($headers));
        error_log('send email: ' . json_encode($message));

        $sendEmailDatas = array(
            'username' => $user_info->data->user_nicename,
            'otp' => $otp,
            'date' => date('Y-m-d H:i:00', strtotime('+5 minutes'))
        );
        $sendEmail = $this->sendEmail('signup', $email, $sendEmailDatas);
        if ($sendEmail === true) {
            $success = array('status' => true, 'error_code' => '0',
                'message' => "Resent email successfully"
            );
        } else {
            //error_log("unable to send email on release_coin order: $order_id");
            $success = array('status' => false, 'error_code' => '1101',
                'message' => "send email got issues"
            );
        }

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($success);
        die();
    }

    public function user_account() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $userdata = $creds = $output = $list_block_users = $blocked = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        //print_r($params);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $output = array('status' => true, 'error_code' => '0',
            'message' => 'success message'
        );

        $isMerchant = (implode('', $user->roles) == 'merchant') ? true : false;
        $isPhone = get_user_meta($user_id, 'isPhoneVerified', true);
        $isEmail = get_user_meta($user_id, 'isEmailVerfied', true);
        $isTradePasswordSet = get_user_meta($user_id, 'isTradePasswordSet', true);
        $is2FAEnabled = get_user_meta($user_id, 'is2FAEnabled', true);
        $isNickname = get_user_meta($user_id, 'isNickname', true);
        switch (get_user_meta($user_id, 'isIdentityVerfied', true)) {
            case 'true':
                $isIdentify = true;
                break;
            case 'pending':
                $isIdentify = 'pending';
                break;
            case 'false':
                $isIdentify = false;
                break;
            default:
                $isIdentify = false;
                break;
        }

        $userTradeRate = $this->backend_trade_rates($user_info->ID);

        $phone = get_user_meta($user_id, 'phone_no', true);
        $userdata = array(
            'data' => array(
                "user_id" => $user_info->ID,
                "username" => $user_info->data->user_nicename,
                "user_nickname" => get_user_meta($user_id, 'nickname', true),
                "email" => $user_info->data->user_email,
                'isMerchant' => $isMerchant,
                'isPhoneVerified' => ($isPhone != '') ? ($isPhone == 'true') ? true : false : false,
                'isEmailVerified' => ($isEmail != '') ? ($isEmail == 'true') ? true : false : false,
                'is2FAEnabled' => ($is2FAEnabled == 'true' || $is2FAEnabled === true) ? true : false,
                "isIdentityVerfied" => $isIdentify,
                "isTradePasswordSet" => ($isTradePasswordSet != '') ? ($isTradePasswordSet == 'true') ? true : false : false,
                "isNickname" => ($isNickname != '') ? ($isNickname == 'true') ? true : false : false,
                "phone" => $phone,
                "trades" => (count($userTradeRate) != 0) ? $userTradeRate['trade'] : '0',
                "trade_rate" => (count($userTradeRate) != 0) ? number_format($userTradeRate['trade_rate'], 2, '.', '') : '0',
            ),
            'bank_list' => array(),
            'block_user' => array(),
            'btc' => array('address' => get_user_meta($user_id, 'btc_address_duplicate', true)),
            'je' => array('address' => get_user_meta($user_id, 'je_address_duplicate', true)),
            'usdt' => array('address' => get_user_meta($user_id, 'usdt_address_duplicate', true)),
        );

        $blockUsers = get_user_meta($user_id, 'block_user', true);
        if ($blockUsers) {
            $explodes = explode(',', $blockUsers);
            if (count($explodes) != 0) {
                foreach ($explodes as $key => $block) {
                    $blocked[$key] = explode('_', $block);
                    $userdata['block_user'][$key] = array(
                        "id" => $blocked[$key][0],
                        "date" => date('Y-m-d H:i', $blocked[$key][1])
                    );
                }
            }
        }

        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function blockedUser() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $userdata = $block_users = $blockUser = $blocks = $blocked = $explodes = $output = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $userBlocked = (isset($params['block_user'])) ? get_userdata($params['block_user']) : '0';
        $block_id = (isset($userBlocked->ID)) ? $userBlocked->ID : '0';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($block_id == $user_id || $block_id == '0') {
            $output = array('status' => false, 'error_code' => '1108', 'message' => 'the given user not available');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Valid user');
        $userdata['data'] = [];

        $userBlockedList = get_user_meta($user_id, 'block_user', true);
        if ($userBlockedList) {

            $blocked = explode(',', $userBlockedList);

            foreach ($blocked as $key => $block) {

                $explodes[$key] = explode('_', $block);

                $blockUser[$explodes[$key][0]] = $block;
                if ($explodes[$key][0] == $block_id) {
                    $output = array('status' => false, 'error_code' => '1108', 'message' => 'the given user not available');

                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
                    print json_encode($output);
                    die();
                    //$this->app_version();
                }

                if ($explodes[$key][0] != $block_id) {

                    if ($block_id != 0) {

                        $blockUser[$block_id] = $block_id . '_' . time();
                    }
                }
            }
            update_user_meta($user_id, 'block_user', implode(',', $blockUser));
        } else {
            update_user_meta($user_id, 'block_user', $block_id . '_' . time());
        }

        $userBlockedList = get_user_meta($user_id, 'block_user', true);

        if ($userBlockedList) {
            $blocks = explode(',', $userBlockedList);
            if (count($blocks) != 0) {
                foreach ($blocks as $key => $block) {
                    $blocking[$key] = explode('_', $block);
                    $users[$key] = get_userdata($blocking[$key][0]);
                    $block_users[$key] = array(
                        'id' => $blocking[$key][0],
                        'name' => (isset($users[$key]->data->user_nicename)) ? $users[$key]->data->user_nicename : '',
                        'date' => date('Y-m-d H:i', $blocking[$key][1])
                    );
                }

                $userdata = array(
                    'data' => $block_users
                );
            }
        }

        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function list_block_user() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $userdata = $users = $block_users = $blocks = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        //print_r($params);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple login detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Valid user');
        $userdata['data'] = [];

        $userBlockedList = get_user_meta($user_id, 'block_user', true);

        if ($userBlockedList) {
            $blocks = explode(',', $userBlockedList);
            if (count($blocks) != 0) {
                foreach ($blocks as $key => $block) {
                    $blocking[$key] = explode('_', $block);
                    $users[$key] = get_userdata($blocking[$key][0]);
                    $block_users[$key] = array(
                        'id' => $blocking[$key][0],
                        'name' => (isset($users[$key]->data->user_nicename)) ? $users[$key]->data->user_nicename : '',
                        'date' => date('Y-m-d H:i', $blocking[$key][1])
                    );
                }

                $userdata = array(
                    'data' => $block_users
                );
            }
        }

        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function unblockUser() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        date_default_timezone_set(LOCAL_TIMEZONE);
        $userdata = $blocked = $unblocked = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        //print_r($params);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $unblockUser = (isset($params['unblock_user'])) ? $params['unblock_user'] : '0';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $counts = 0;
        $userBlockedList = get_user_meta($user_id, 'block_user', true);
        if ($userBlockedList) {
            $blocks = explode(',', $userBlockedList);
            if (count($blocks) != 0) {
                foreach ($blocks as $key => $block) {
                    $blocking[$key] = explode('_', $block);
                    $blocked[$blocking[$key][0]] = $block;

                    if ($unblockUser == $blocking[$key][0]) {
                        $counts += 1;
                        unset($blocked[$blocking[$key][0]]);
                    }
                }
            }
        }
        if ($counts == 0 || $counts == '0') {
            $output = array('status' => false, 'error_code' => '1108', 'message' => 'the given user not available');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
        update_user_meta($user_id, 'block_user', implode(',', $blocked));


        $output = array('status' => true, 'error_code' => '0', 'message' => 'Valid user');
        $userdata['data'] = [];

        $userBlockedList = get_user_meta($user_id, 'block_user', true);

        if ($userBlockedList) {
            $blocks = explode(',', $userBlockedList);
            if (count($blocks) != 0) {
                foreach ($blocks as $key => $block) {
                    $blocking[$key] = explode('_', $block);
                    $users[$key] = get_userdata($blocking[$key][0]);
                    $block_users[$key] = array(
                        'id' => $blocking[$key][0],
                        'name' => (isset($users[$key]->data->user_nicename)) ? $users[$key]->data->user_nicename : '',
                        'date' => date('Y-m-d H:i', $blocking[$key][1])
                    );
                }

                $userdata = array(
                    'data' => $block_users
                );
            }
        }


        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function account_balance() {
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $userdata = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $unblockUser = (isset($params['unblock_user'])) ? $params['unblock_user'] : '0';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $BTC_Wallet = new User_Coin_Wallet();
        $Je_Wallet = new User_Coin_JE_Wallet();
        $USDT_Wallet = new User_Coin_USDT_Wallet();

        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', AUTH_SALT);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);

        $curl = curl_init();
        $btc_address = get_user_meta($user_info->ID, 'btc_address_duplicate', true);
        if ($btc_address == '' || $btc_address == null) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_BTC . "&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $btc_curl = curl_exec($curl);
            $btc_checkingAddress = json_decode($btc_curl);
            if (strtolower($je_checkingAddress->status) == "success") {
                $btc_encryptedprivateKey = base64_encode(openssl_encrypt($btc_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                update_user_meta($user_info->ID, 'btc_address', $btc_encryptedprivateKey);
                update_user_meta($user_info->ID, 'btc_address_duplicate', $btc_checkingAddress->data->address);
            }
        }

        $je_address = get_user_meta($user_info->ID, 'je_address_duplicate', true);
        if ($je_address == '' || $je_address == null) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_JE . "&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $je_curl = curl_exec($curl);
            $je_checkingAddress = json_decode($je_curl);
            if (strtolower($je_checkingAddress->status) == "success") {
                $je_encryptedprivateKey = base64_encode(openssl_encrypt($je_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                update_user_meta($user_info->ID, 'je_address', $je_encryptedprivateKey);
                update_user_meta($user_info->ID, 'je_address_duplicate', $je_checkingAddress->data->address);
            }
        }

        $usdt_address = get_user_meta($user_info->ID, 'usdt_address_duplicate', true);
        if ($usdt_address == '' || $usdt_address == null) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_JE . "&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $usdt_curl = curl_exec($curl);
            $usdt_checkingAddress = json_decode($usdt_curl);
            if (strtolower($usdt_checkingAddress->status) == "success") {
                $usdt_encryptedprivateKey = base64_encode(openssl_encrypt($usdt_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                update_user_meta($user_info->ID, 'usdt_address', $usdt_encryptedprivateKey);
                update_user_meta($user_info->ID, 'usdt_address_duplicate', $usdt_checkingAddress->data->address);
            }
        }

        curl_close($curl);

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Valid user');
        $userdata = array(
            'data' => array(
                'btc' => array(
                    'btc_available' => $BTC_Wallet->get_user_wallet_available_balance($user_info->ID), //asdf//
                    'btc_freeze' => number_format($BTC_Wallet->get_user_wallet_freeze_balanced($user_info->ID), 8, '.', ''), //asdf//
                    'coin_address' => get_user_meta($user_info->ID, 'btc_address_duplicate', true)
                ),
                'je' => array(
                    'je_available' => $Je_Wallet->get_user_wallet_available_balance($user_info->ID),
                    'je_freeze' => $Je_Wallet->get_user_wallet_freeze_balanced($user_info->ID),
                    'coin_address' => get_user_meta($user_info->ID, 'je_address_duplicate', true)
                ),
                'usdt' => array(
                    'usdt_available' => $USDT_Wallet->get_user_wallet_available_balance($user_info->ID),
                    'usdt_freeze' => $USDT_Wallet->get_user_wallet_freeze_balanced($user_info->ID),
                    'coin_address' => get_user_meta($user_info->ID, 'usdt_address_duplicate', true)
                )
            )
        );

        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function account_history() { //modified//
        //$this->app_version();
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $userdata = $histories = [];
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $unblockUser = (isset($params['unblock_user'])) ? $params['unblock_user'] : '0';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
        $output = array('status' => true, 'error_code' => '0', 'message' => 'Valid user');

        /** Developer side * */
        $BTC_Wallet = new User_Coin_Wallet();
        $JE_Wallet = new User_Coin_JE_Wallet();
        $USDT_Wallet = new User_Coin_USDT_Wallet();

        $relaseCoin_History = [];

        $BTC_History = $BTC_histories = $BTC_Types = [];
        $BTC_History = $BTC_Wallet->get_account_histories($user_id);
        $BTC_ReleaseCoin_History = $BTC_Wallet->release_coin_history($user_id);
        if (count($BTC_ReleaseCoin_History) != 0) {
            foreach ($BTC_ReleaseCoin_History as $key => $rch) {
                $relaseCoin_History[$key] = array(
                    'id' => $rch->id,
                    'coin_type' => 'btc',
                    'amount' => $rch->amount,
                    'refid' => '0',
                    'post_type' => "release coin",
                    'date' => $this->convertToUTCDate($rch->created)
                );
            }
        }
        if (count($BTC_History) != 0) {
            foreach ($BTC_History as $history) {
                switch ($history->type) {
                    case '1':
                        $BTC_Types[$history->id] = 'deposit';
                        break;
                    case '2':
                        $BTC_Types[$history->id] = 'post ads [sell]';
                        break;
                    case '3':
                        $BTC_Types[$history->id] = 'withdraw';
                        break;
                    case '4':
                        $BTC_Types[$history->id] = 'received coin';
                        break;
                    case '5':
                        $BTC_Types[$history->id] = 'post ads [buy]';
                        break;
                    case '6':
                        $BTC_Types[$history->id] = 'release coin';
                        break;
                    case '7':
                        $BTC_Types[$history->id] = 'editing post ads [increment]';
                        break;
                    case '8':
                        $BTC_Types[$history->id] = 'editing post ads [decrement]';
                        break;

                    default:
                        $BTC_Types[$history->id] = '';
                        break;
                }
                if ($history->type != '5') {
                    $BTC_histories[$history->id] = array(
                        'id' => $history->id,
                        'coin_type' => 'btc',
                        'amount' => $history->amount,
                        'refid' => $history->refid,
                        'post_type' => $BTC_Types[$history->id],
                        'date' => $this->convertToUTCDate($history->datetime)
                    );
                }
            }
            $BTC_histories = array_merge($BTC_histories, $relaseCoin_History);
            $histories = $this->resortingArray($histories, $BTC_histories);
        }


        $JE_History = $JE_histories = $JE_Types = [];
        $JE_History = $JE_Wallet->get_account_histories($user_id);
        $JE_ReleaseCoin_History = $JE_Wallet->release_coin_history($user_id);

        if (count($JE_ReleaseCoin_History) != 0) {
            foreach ($JE_ReleaseCoin_History as $key => $rch) {
                $relaseCoin_History[$key] = array(
                    'id' => $rch->id,
                    'coin_type' => 'je',
                    'amount' => $rch->amount,
                    'refid' => '0',
                    'post_type' => "release coin",
                    'date' => $this->convertToUTCDate($rch->created)
                );
            }
        }


        if (count($JE_History) != 0) {
            foreach ($JE_History as $history) {
                switch ($history->type) {
                    case '1':
                        $JE_Types[$history->id] = 'deposit';
                        break;
                    case '2':
                        $JE_Types[$history->id] = 'post ads [sell]';
                        break;
                    case '3':
                        $JE_Types[$history->id] = 'withdraw';
                        break;
                    case '4':
                        $JE_Types[$history->id] = 'received coin';
                        break;
                    case '5':
                        $JE_Types[$history->id] = 'post ads [buy]';
                        break;
                    case '6':
                        $JE_Types[$history->id] = 'release coin';
                        break;
                    case '7':
                        $JE_Types[$history->id] = 'editing post ads [increment]';
                        break;
                    case '8':
                        $JE_Types[$history->id] = 'editing post ads [decrement]';
                        break;

                    default:
                        $JE_Types[$history->id] = '';
                        break;
                }
                if ($history->type != '5') {
                    $JE_histories[$history->id] = array(
                        'id' => $history->id,
                        'coin_type' => 'je',
                        'amount' => $history->amount,
                        'refid' => $history->refid,
                        'post_type' => $JE_Types[$history->id],
                        'date' => $this->convertToUTCDate($history->datetime)
                    );
                }
            }
            $JE_histories = array_merge($JE_histories, $relaseCoin_History);
            $histories = $this->resortingArray($histories, $JE_histories);
        }


        $USDT_History = $USDT_histories = $USDT_Types = [];
        $USDT_History = $USDT_Wallet->get_account_histories($user_id);
        $USDT_ReleaseCoin_History = $USDT_Wallet->release_coin_history($user_id);

        if (count($USDT_ReleaseCoin_History) != 0) {
            foreach ($USDT_ReleaseCoin_History as $key => $rch) {
                $relaseCoin_History[$key] = array(
                    'id' => $rch->id,
                    'coin_type' => 'usdt',
                    'amount' => $rch->amount,
                    'refid' => '0',
                    'post_type' => "release coin",
                    'date' => $this->convertToUTCDate($rch->created)
                );
            }
        }
        if (count($USDT_History) != 0) {
            foreach ($USDT_History as $history) {
                switch ($history->type) {
                    case '1':
                        $USDT_Types[$history->id] = 'deposit';
                        break;
                    case '2':
                        $USDT_Types[$history->id] = 'post ads [sell]';
                        break;
                    case '3':
                        $USDT_Types[$history->id] = 'withdraw';
                        break;
                    case '4':
                        $USDT_Types[$history->id] = 'received coin';
                        break;
                    case '5':
                        $USDT_Types[$history->id] = 'post ads [buy]';
                        break;
                    case '6':
                        $USDT_Types[$history->id] = 'release coin';
                        break;
                    case '7':
                        $USDT_Types[$history->id] = 'editing post ads [increment]';
                        break;
                    case '8':
                        $USDT_Types[$history->id] = 'editing post ads [decrement]';
                        break;

                    default:
                        $USDT_Types[$history->id] = '';
                        break;
                }
                if ($history->type != '5') {
                    $USDT_histories[$history->id] = array(
                        'id' => $history->id,
                        'coin_type' => 'usdt',
                        'amount' => $history->amount,
                        'refid' => $history->refid,
                        'post_type' => $USDT_Types[$history->id],
                        'date' => $this->convertToUTCDate($history->datetime)
                    );
                }
            }
            $USDT_histories = array_merge($USDT_histories, $relaseCoin_History);
            $histories = $this->resortingArray($histories, $USDT_histories);
        }

        $userdata['data'] = $histories;

        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function status_identification() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $isIdentification = get_user_meta($user_id, 'isIdentityVerfied', true);
        $identify_status = get_user_meta($user_id, 'identification_status', true);
        $image_front = get_user_meta($user_id, 'image_front_status', true);
        $image_back = get_user_meta($user_id, 'image_back_status', true);
        $image_verify = get_user_meta($user_id, 'image_verify_status', true);

        if ($identify_status == '') {
            update_user_meta($user_id, 'isIdentityVerfied', 'false');
            $isIdentification = false;
        }

        $output = array(
            'status' => true,
            'error_code' => '0',
            'message' => 'Successfully',
            'data' => array(
                "user_id" => $user_id,
                "isIdentifyVerified" => ($isIdentification != '' && $isIdentification == 'true') ? true : false,
                "identification_status" => ($identify_status != '') ? $identify_status : 'unverified',
                "image_front_status" => ($image_front != '') ? ($image_front == 'true') ? true : false : false,
                "image_back_status" => ($image_back != '') ? ($image_back == 'true') ? true : false : false,
                "image_verify_status" => ($image_verify != '') ? ($image_verify == 'true') ? true : false : false,
            )
        );

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function user_identification() {
        error_log("===== user_identification ===\n");
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $upload_dir = wp_upload_dir();
        // print_r($upload_dir);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $first_name = (isset($params['first_name'])) ? $params['first_name'] : '';
        $last_name = (isset($params['last_name'])) ? $params['last_name'] : '';
        $user_identification = (isset($params['user_identification'])) ? $params['user_identification'] : '';
        //$bankslip=$params['bankslip'];

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            error_log("POST: ");
            error_log($_POST);
            error_log("$uat != $tokens || $uat == '' \n");
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($first_name == '' || $last_name == '' || $user_identification == '') {
            error_log("POST: ");
            error_log($_POST);
            error_log("$first_name == '' || $last_name == '' || $user_identification == '' \n");
            error_log("===== user_identification ===");
            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $path = "uploads/" . $user_id;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $slip = array();

        $name = $_FILES['front_page']['name'];
        $tmpname = $_FILES['front_page']['tmp_name'];

        error_log("front_page\n");
        error_log($_FILES);
        error_log($name);

        $filename = time() . $name;
        move_uploaded_file($tmpname, $path . "/" . $filename);

        //$uploaddir=wp_upload_dir();
        $slip['front_page'] = get_option('siteurl') . "/uploads/" . $user_id . "/" . $filename;
        update_user_meta($user_id, 'image_front_page', $path . "/" . $filename);
        update_user_meta($user_id, 'image_url_front_page', $slip['front_page']);

        $name = $_FILES['end_page']['name'];
        $tmpname = $_FILES['end_page']['tmp_name'];

        error_log("\nend_page\n");
        error_log($_FILES);
        error_log($name);

        $filename = time() . $name;
        move_uploaded_file($tmpname, $path . "/" . $filename);

        //$uploaddir=wp_upload_dir();
        $slip['end_page'] = get_option('siteurl') . "/uploads/" . $user_id . "/" . $filename;
        update_user_meta($user_id, 'image_end_page', $path . "/" . $filename);
        update_user_meta($user_id, 'image_url_end_page', $slip['end_page']);

        $name = $_FILES['verify_page']['name'];
        $tmpname = $_FILES['verify_page']['tmp_name'];

        error_log("\verify_page\n");
        error_log($_FILES);
        error_log($name);

        $filename = time() . $name;
        move_uploaded_file($tmpname, $path . "/" . $filename);

        //$uploaddir=wp_upload_dir();
        $slip['verify_page'] = get_option('siteurl') . "/uploads/" . $user_id . "/" . $filename;
        update_user_meta($user_id, 'image_verify_page', $path . "/" . $filename);
        update_user_meta($user_id, 'image_url_verify_page', $slip['verify_page']);

        /* $update="update {$this->db->prefix}sales_user_request set status='paid' where id='$requesid'";
          $this->db->query($update);

          $User_Sales_and_Finance->insert_requestmeta($requesid,"creditdeposit",$amounthweset);



          $User_Sales_and_Finance->insert_requestmeta($requesid,"bank_slip",serialize($slip));
         */

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Verification completed');

        update_user_meta($user_id, 'verify_first_name', $first_name);
        update_user_meta($user_id, 'verify_last_name', $last_name);
        update_user_meta($user_id, 'verify_identification', $user_identification);
        update_user_meta($user_id, 'isIdentityVerfied', 'false');
        update_user_meta($user_id, 'identification_status', 'processing');

        $userdata = array(
            'data' => array(
                "user_id" => $user_info->ID,
                "isIdentifyVerified" => false,
                "identification_status" => 'processing'
            )
        );

        //[zxcv] pending for identify//
        $sendEmail = $this->sendEmail('identify', $user_info->data->user_email, 'You has been requesting for verify Identification');
        if ($sendEmail === false) {
            error_log("unable to send email on paid order: $order_id");
        }

        error_log("===== user_identification ===\n");

        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function edit_identification() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);
        $upload_dir = wp_upload_dir();
        // print_r($upload_dir);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $isIdentification = get_user_meta($user_id, 'isIdentityVerfied', true);
        $isIdentification = ($isIdentification != '' && $isIdentification == 'true') ? true : false;
        $identify_status = get_user_meta($user_id, 'identification_status', true);
        $image_front = get_user_meta($user_id, 'image_front_status', true);
        $image_back = get_user_meta($user_id, 'image_back_status', true);
        $image_verify = get_user_meta($user_id, 'image_verify_status', true);

        if ($isIdentification == 'true' || $isIdentification === true) {
            $output = array('status' => false, 'error_code' => '1101', 'message' => 'Unable to process');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($identify_status == 'pending' || $identify_status == 'rejected') {
            $path = "uploads/" . $user_id;
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $slip = array();

            if ($image_front == '' || $image_front == 'false' || $image_front === false) {
                $front_pagename = $_FILES['front_page']['name'];
                $front_pagetmpname = $_FILES['front_page']['tmp_name'];

                $filename = time() . $front_pagename;
                move_uploaded_file($front_pagetmpname, $path . "/" . $filename);
                $slip['front_page'] = get_option('siteurl') . "/uploads/" . $user_id . "/" . $filename;
                update_user_meta($user_id, 'image_front_page', $path . "/" . $filename);
                update_user_meta($user_id, 'image_url_front_page', $slip['front_page']);
            }

            if ($image_back == '' || $image_back == 'false' || $image_back === false) {
                $end_pagename = $_FILES['end_page']['name'];
                $end_pagetmpname = $_FILES['end_page']['tmp_name'];

                $filename = time() . $end_pagename;
                move_uploaded_file($end_pagetmpname, $path . "/" . $filename);

                $slip['end_page'] = get_option('siteurl') . "/uploads/" . $user_id . "/" . $filename;
                update_user_meta($user_id, 'image_end_page', $path . "/" . $filename);
                update_user_meta($user_id, 'image_url_end_page', $slip['end_page']);
            }

            if ($image_verify == '' || $image_verify == 'false' || $image_verify === false) {
                $verify_pagename = $_FILES['verify_page']['name'];
                $verify_pagetmpname = $_FILES['verify_page']['tmp_name'];

                $filename = time() . $verify_pagename;
                move_uploaded_file($verify_pagetmpname, $path . "/" . $filename);

                $slip['verify_page'] = get_option('siteurl') . "/uploads/" . $user_id . "/" . $filename;
                update_user_meta($user_id, 'image_verify_page', $path . "/" . $filename);
                update_user_meta($user_id, 'image_url_verify_page', $slip['verify_page']);
            }

            update_user_meta($user_id, 'isIdentifyVerified', 'false');
            update_user_meta($user_id, 'identification_status', 'processing');

            $output = array(
                'status' => true,
                'error_code' => '0',
                'message' => 'User Identification processing',
                'data' => array(
                    "user_id" => $user_id,
                    "isIdentifyVerified" => false,
                    "identification_status" => 'processing'
                )
            );

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        } else {
            $output = array('status' => false, 'error_code' => '1101', 'message' => 'Unable to process');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
    }

    public function bank_account() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $totalBanks = $newTotalBanks = 0;
        $userdata = $bankDatas = [];
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';

        $bank_id = (isset($params['bank_id'])) ? $params['bank_id'] : '';
        $bank_status = (isset($params['bank_status'])) ? (strtolower($params['bank_status']) == 'delete' || strtolower($params['bank_status']) == 'deleted') ? 'deactive' : 'active' : 'active';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        $getTotalBanks = get_user_meta($user_id, 'total_banks', true);
        $totalBanks = ($getTotalBanks == '' || $getTotalBanks == null) ? '0' : $getTotalBanks;

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($bank_id != '' && $bank_id != null) {
            if ($bank_status != 'active') {
                $output = array('status' => true, 'error_code' => '0', 'message' => 'Deleted bank successfully');
            } else {
                $output = array('status' => true, 'error_code' => '0', 'message' => 'Add bank successfully');
            }

            update_user_meta($user_id, "bank_status_$bank_id", $bank_status);
        } else {
            $output = array('status' => true, 'error_code' => '0', 'message' => 'Add bank successfully');
            $bank_holder_name = (isset($params['bank_holder_name'])) ? $params['bank_holder_name'] : '';
            $bank_name = (isset($params['bank_name'])) ? $params['bank_name'] : '';
            $bank_branch = (isset($params['bank_branch'])) ? $params['bank_branch'] : '';
            $bank_swift_code = (isset($params['bank_swift_code'])) ? $params['bank_swift_code'] : '';
            $bank_account_no = (isset($params['bank_account_no'])) ? $params['bank_account_no'] : '';
            $bank_country = (isset($params['bank_country'])) ? $params['bank_country'] : '';
            $bank_currency = (isset($params['bank_currency'])) ? $params['bank_currency'] : '';

            if ($totalBanks == 0) {
                update_user_meta($user_id, "own_bank_account_no", $bank_account_no);
                update_user_meta($user_id, "own_bank_bname", $bank_name);
                update_user_meta($user_id, "own_bank_country", $bank_country);
                update_user_meta($user_id, "own_bank_swiftcode", $bank_swift_code);
                update_user_meta($user_id, "own_bank_country", $bank_country);
                update_user_meta($user_id, "own_bank_cuurency", $bank_currency);
            }

            $totalBanks = $totalBanks + 1;
            update_user_meta($user_id, "total_banks", $totalBanks);
            update_user_meta($user_id, "bank_id_$totalBanks", $totalBanks);
            update_user_meta($user_id, "bank_holder_name_$totalBanks", $bank_holder_name);
            update_user_meta($user_id, "bank_name_$totalBanks", $bank_name);
            update_user_meta($user_id, "bank_branch_$totalBanks", $bank_branch);
            update_user_meta($user_id, "bank_swift_code_$totalBanks", $bank_swift_code);
            update_user_meta($user_id, "bank_account_no_$totalBanks", $bank_account_no);
            update_user_meta($user_id, "bank_country_$totalBanks", $bank_country);
            update_user_meta($user_id, "bank_currency_$totalBanks", $bank_currency);
            update_user_meta($user_id, "bank_status_$totalBanks", 'active');
        }


        $userdata['data'] = [];
        $newTotalBanks = get_user_meta($user_id, 'total_banks', true);

        for ($i = 1; $i <= $totalBanks; $i++) {
            if (get_user_meta($user_id, "bank_status_$i", true) == 'active') {
                array_push($bankDatas, array(
                    "id" => get_user_meta($user_id, "bank_id_$i", true),
                    "bank_holder_name" => get_user_meta($user_id, "bank_holder_name_$i", true),
                    "bank_name" => get_user_meta($user_id, "bank_name_$i", true),
                    "bank_branch" => get_user_meta($user_id, "bank_branch_$i", true),
                    "bank_swift_code" => get_user_meta($user_id, "bank_swift_code_$i", true),
                    "bank_account_no" => get_user_meta($user_id, "bank_account_no_$i", true),
                    "bank_country" => get_user_meta($user_id, "bank_country_$i", true),
                    "bank_currency" => get_user_meta($user_id, "bank_currency_$i", true)
                ));
            }
        }

        $userdata['data'] = $bankDatas;
        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function bank_account_list() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $totalBanks = 0;
        $bankList = $userdata = $bankDatas = [];
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        $getTotalBanks = get_user_meta($user_id, 'total_banks', true);
        $totalBanks = ($getTotalBanks == '' || $getTotalBanks == null) ? '0' : $getTotalBanks;

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Bank List');
        $userdata['data'] = [];

        for ($i = 1; $i <= $totalBanks; $i++) {
            if (get_user_meta($user_id, "bank_status_$i", true) == 'active') {
                array_push($bankDatas, array(
                    "id" => get_user_meta($user_id, "bank_id_$i", true),
                    "bank_holder_name" => get_user_meta($user_id, "bank_holder_name_$i", true),
                    "bank_name" => get_user_meta($user_id, "bank_name_$i", true),
                    "bank_branch" => get_user_meta($user_id, "bank_branch_$i", true),
                    "bank_swift_code" => get_user_meta($user_id, "bank_swift_code_$i", true),
                    "bank_account_no" => get_user_meta($user_id, "bank_account_no_$i", true),
                    "bank_country" => get_user_meta($user_id, "bank_country_$i", true),
                    "bank_currency" => get_user_meta($user_id, "bank_currency_$i", true)
                ));
            }
        }

        $userdata['data'] = $bankDatas;
        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        print json_encode($output);
        die();
    }

    //wallet withdraw api
    public function wallet_withdraw_request_confirm() { //need to change name//
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $coinAddress = $availables_balance = $freeze_balance = $types = '';
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $coin_type = (isset($params['coin_type'])) ? strtoupper($params['coin_type']) : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($coin_type == '' || $coin_type == null) {
            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        switch (strtoupper($coin_type)) {
            case 'BTC':
                $types = '1';
                $CryptoWallet = new User_Coin_Wallet();
                $coinAddress = get_user_meta($user_id, 'btc_address_duplicate', true);
                break;

            case 'JE':
                $types = '2';
                $CryptoWallet = new User_Coin_JE_Wallet();
                $coinAddress = get_user_meta($user_id, 'je_address_duplicate', true);
                break;

            case 'USDT':
                $types = '2';
                $CryptoWallet = new User_Coin_USDT_Wallet();
                $coinAddress = get_user_meta($user_id, 'usdt_address_duplicate', true);
                break;

            default:
                $types = '0';
                $CryptoWallet = new User_Coin_Wallet();
                $coinAddress = '';
                $availables_balance = '';
                $freeze_balance = '';
                break;
        }

        $availables_balance = $CryptoWallet->get_user_wallet_available_balance($user_info->ID); //asdf//
        $freeze_balance = $CryptoWallet->get_user_wallet_freeze_balanced($user_info->ID); //asdf//

        $account_holder_name = $user_info->display_name;

        $params['tokens'] = INTERNAL_TOKEN;
        $params['types'] = $types;

        $total_wallet_balance = $CryptoWallet->get_user_wallet_balance($user_id);

        $fees = $this->getFees($params);

        $output = array('status' => true, 'error_code' => '0', 'message' => 'success');
        $additional_data = array(
            'data' => array(
                'coin_type' => $coin_type,
                'balance' => $total_wallet_balance,
                'fees' => (count($fees) != 0) ? $fees['fees']->fee : '',
                'limits' => 0,
                'max_limit' => 100,
                'withdraw_details' => array(
                    strtolower($coin_type) . '_available' => $availables_balance,
                    strtolower($coin_type) . '_freeze' => $freeze_balance,
                    'coin_address' => $coinAddress
                )
            )
        );



        $output = array_merge($output, $additional_data);
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    //function wallet_withdraw_request_submit() {
    function wallet_withdraw_request_submit() { //need to change name//
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $coin_type = (isset($params['coin_type'])) ? strtoupper($params['coin_type']) : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        $invalid_withdraw = get_user_meta($user_id, 'disabled_withdraw', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($invalid_withdraw != '' && time() < ($invalid_withdraw + (3600 * 24))) {
            $output = array('status' => false, 'error_code' => '1101', 'message' => 'Your current status unable to withdraw (below 24 hours)');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($coin_type == '' || $coin_type == null) {
            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }


        $trading_pin = trim($params['trade_password']);
        $amount = $params['amount'];
        $type = '3';
        $coin_address = $params['coin_address'];

        switch (strtoupper($coin_type)) {
            case 'BTC':
                $CyrptoWallet = new User_Coin_Wallet();
                $wallet_request_task = $this->db->prefix . 'btc_wallet_request_task';
                $wallet_request_task_meta = $this->db->prefix . 'btc_wallet_request_task_meta';
                $wallet_request_task_transaction_log = $this->db->prefix . 'btc_wallet_request_task_transaction_log';
                break;

            case 'JE':
                $CyrptoWallet = new User_Coin_JE_Wallet();
                $wallet_request_task = $this->db->prefix . 'je_wallet_request_task';
                $wallet_request_task_meta = $this->db->prefix . 'je_wallet_request_task_meta';
                $wallet_request_task_transaction_log = $this->db->prefix . 'je_wallet_request_task_transaction_log';
                break;

            case 'USDT':
                $CyrptoWallet = new User_Coin_USDT_Wallet();
                $wallet_request_task = $this->db->prefix . 'usdt_wallet_request_task';
                $wallet_request_task_meta = $this->db->prefix . 'usdt_wallet_request_task_meta';
                $wallet_request_task_transaction_log = $this->db->prefix . 'usdt_wallet_request_task_transaction_log';
                break;

            default:
                break;
        }

        $min_withdraw = '0';
        $params['tokens'] = INTERNAL_TOKEN;
        $coinSettings = $this->getFees($params);
        if (count($coinSettings) != 0 || (isset($coinSettings->status))) {
            if ($coinSettings->status === true || $coinSettings->status == true) {
                $min_withdraw = $coinSettings->fees->fee;
            }
        }

        $user_secret_pin = get_the_author_meta('duplicate_secret_pin', $user_id);

        if ($trading_pin != $user_secret_pin) {
            $output = array('status' => false, 'error_code' => '1111', 'message' => 'invalid password');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        } else {
            $total_wallet_balance = $CyrptoWallet->get_user_wallet_balance($user_id);
            if ($total_wallet_balance >= $amount) {
                if ($amount >= $min_withdraw) {
                    $check_daily_withdraw = "SELECT SUM(meta_value) as total_amount FROM $wallet_request_task_meta WHERE request_id IN (SELECT id FROM $wallet_request_task WHERE DATE(datetime) = CURDATE() AND user = '$user_id' AND type = '3' and id NOT IN(SELECT request_id FROM $wallet_request_task_transaction_log WHERE SUBSTRING(transaction, 1, 4) = 'Sell' OR SUBSTRING(transaction, 1, 3) = 'Buy') ) AND meta_key = 'amount'";

                    $result_check_daily_withdraw = $this->db->get_results($check_daily_withdraw);
                    $total_daily_amount = $result_check_daily_withdraw[0]->total_amount + $coins_withdraw_amount;
                    if ($total_daily_amount <= 5) {
                        $type = '3';
                        $status = 'pending';
                        $operator = '';
                        $refid = get_option("hwe_" . strtolower($coin_type) . "_wallet_request_refid");
                        if (empty($refid)) {
                            update_option('hwe_".strtolower($coin_type)."_wallet_request_refid', 1);
                            $refid = 1;
                        } else {
                            update_option('hwe_".strtolower($coin_type)."_wallet_request_refid', $refid + 1);
                            $refid = $refid + 1;
                        }

                        $withdrawDate = date('Y-m-d H:i:s', time());
                        $request_id = $CyrptoWallet->insert_request($refid, $user_id, $type, $status, $operator);
                        $CyrptoWallet->add_request_meta($request_id, 'coins_account_address', $coin_address);
                        $CyrptoWallet->add_request_meta($request_id, 'amount', $amount);
                        $CyrptoWallet->add_request_meta($request_id, 'date', $withdrawDate);

                        $transaction = 'Withdraw to (' . $coin_address . ')';
                        $CyrptoWallet->user_withdraw_request_add_logs($request_id, $transaction, $user_id, $amount);


                        $sendEmailDatas = array(
                            'username' => $user_info->data->user_nicename,
                            'amount' => $amount,
                            'coin_type' => $coin_type,
                            'address' => $coin_address,
                            'date' => $withdrawDate
                        );
                        $sendEmail = $this->sendEmail('withdraw', $user_info->data->user_email, $sendEmailDatas);
                        if ($sendEmail === false) {
                            error_log("send email for withdraws problems: " . $user_info->data->user_email);
                        }

                        $output = array('status' => true, 'error_code' => '0', 'message' => 'Request successfully submitted.');
                        $additional_data = array(
                            'data' => array(
                                'refid' => $refid
                            )
                        );



                        $output = array_merge($output, $additional_data);
                        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                        header('Content-type: application/json');
                        header('Access-Control-Allow-Origin: *');
                        print json_encode($output);
                        die();
                    } else {
                        $output = array('status' => false, 'error_code' => '1101', 'message' => 'You have exceeded daily withdrawal amount limit.');
                        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                        header('Content-type: application/json');
                        header('Access-Control-Allow-Origin: *');
                        print json_encode($output);
                        die();
                    }
                } else {
                    $output = array('status' => false, 'error_code' => '1101', 'message' => 'You cannot withdraw less from 0.001 coins.');
                    header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                    header('Content-type: application/json');
                    header('Access-Control-Allow-Origin: *');
                    print json_encode($output);
                    die();
                }
            } else {
                $output = array('status' => false, 'error_code' => '1101', 'message' => 'You have insufficient balance in your wallet.');
                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');
                print json_encode($output);
                die();
            }
        }
    }

    public function merchant_details() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $merchant_id = (isset($_POST['merchant_id'])) ? $_POST['merchant_id'] : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        // if($uat == ''){
        //     $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');
        //     header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        //     header('Content-type: application/json');
        //     header('Access-Control-Allow-Origin: *');
        //     print json_encode($output);
        //     die();
        // }
        // if ($uat != $tokens) {
        //     $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');
        // header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        //     header('Content-type: application/json');
        //     header('Access-Control-Allow-Origin: *');
        //     print json_encode($output);
        //     die();
        // }

        $merchant_info = get_userdata($merchant_id);
        $merchant_roles = (isset($merchant_info->roles)) ? implode('', $merchant_info->roles) : '';
        if ($merchant_roles != 'merchant' || $merchant_roles == '') {
            $output = array('status' => false, 'error_code' => '1101', 'message' => 'This user is not a merchant');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
        $success = array('status' => true, 'error_code' => '0', 'message' => "merchant details");

        $isPhone = get_user_meta($merchant_id, 'isPhoneVerified', true);
        $isEmail = get_user_meta($merchant_id, 'isEmailVerfied', true);
        switch (get_user_meta($merchant_id, 'isIdentityVerfied', true)) {
            case 'true':
                $isIdentify = true;
                break;
            case 'pending':
                $isIdentify = 'pending';
                break;
            case 'false':
                $isIdentify = false;
                break;
            default:
                $isIdentify = false;
                break;
        }

        $userdata = array(
            'data' => array(
                "merchant_id" => $merchant_id,
                "profiles" => '',
                "username" => $merchant_info->data->user_nicename,
                "user_nickname" => get_user_meta($merchant_id, 'nickname', true),
                "trades" => '0',
                "trade_rates" => '0',
                "isPhoneVerified" => ($isPhone != '') ? ($isPhone == 'true') ? true : false : false,
                "isEmailVerfied" => ($isEmail != '') ? ($isEmail == 'true') ? true : false : false,
                "isIdentityVerfied" => $isIdentify,
            )
        );
        $output = array_merge($success, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function resortingArray($previous, $array) {
        $newArray = $anotherArray = [];
        $i = 0;
        if (count($previous) != 0) {
            $array = array_merge($array, $previous);
        }
        if (count($array) != 0) {
            foreach ($array as $r) {
                $newArray[$i] = $r;
                $i++;
            }

            $anotherArray = $this->sortArray($newArray, 'date');
            return $anotherArray;
        }
        return $newArray;
    }

    public function messagesend() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $userid = (isset($params['userid'])) ? $params['userid'] : '';
        $merchantid = (isset($params['merchant_id'])) ? $params['merchant_id'] : '';
        $message = (isset($params['message'])) ? $params['message'] : '';
        $post_id = (isset($params['post_id'])) ? $params['post_id'] : '';

        $user_info = get_userdata($userid);
        $tokens = get_user_meta($userid, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $chattable = $this->db->prefix . "chatsession";
        $chatsession_meta = $this->db->prefix . "chatsession_meta";

        $insert = "insert into $chattable set userid='$userid',merchantid='$merchantid',datetime=NOW()";
        $this->db->query($insert);
        $chatid = $this->db->insert_id;

        $insert = "insert into $chatsession_meta set chatid='$chatid',meta_key='message',meta_value='$message'";
        $this->db->query($insert);

        $insert = "insert into $chatsession_meta set chatid='$chatid',meta_key='postid',meta_value='$post_id'";
        $this->db->query($insert);


        $output = array('status' => true, 'error_code' => '0', 'message' => 'Request successfully submitted.');

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function chatlist() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $userid = (isset($params['userid'])) ? $params['userid'] : '';
        $postid = (isset($params['post_id'])) ? $params['post_id'] : '';

        $user_info = get_userdata($userid);
        $tokens = get_user_meta($userid, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $chattable = $this->db->prefix . "chatsession";
        $chatsession_meta = $this->db->prefix . "chatsession_meta";

        $select = "select * from $chattable as a, $chatsession_meta as b where  a.id=b.chatid and (a.userid='$userid' or a.merchantid='$userid') and  b.meta_key='postid' and b.meta_value='$postid' order by a.datetime asc ";
        $results = $this->db->get_results($select);
        $messagelist = array();
        foreach ($results as $value) {
            $chatid = $value->chatid;
            $userid = $value->userid;
            $fromuser = get_userdata($userid);
            $fromdisplayname = $fromuser->display_name;

            $merchantid = $value->merchantid;
            $touser = get_userdata($merchantid);
            $todisplayname = $touser->display_name;

            $select = "select * from $chatsession_meta where chatid='$chatid' and meta_key='message'";
            $messagereslult = $this->db->get_results($select);
            $message = $messagereslult[0]->meta_value;

            array_push($messagelist, array("from" => $fromdisplayname, "to" => $todisplayname, "message" => $message));
        }


        $output = array('status' => true, 'error_code' => '0', 'message' => 'Request successfully submitted.', 'data' => $messagelist);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function check_trade_pin() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $trading_pin = (isset($_POST['trade_password'])) ? $_POST['trade_password'] : '';

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $user_secret_pin = get_the_author_meta('duplicate_secret_pin', $user_id);

        if ($trading_pin != $user_secret_pin) {
            $output = array('status' => false, 'error_code' => '1111', 'message' => 'invalid password');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }
        $output = array('status' => true, 'error_code' => '0', 'message' => 'Secret pin is correct.');
        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function enable_nickname() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $headers = $this->getallheaders();
        $params = filter_input_array(INPUT_POST, $_POST);

        $uat = (isset($headers['uat'])) ? $headers['uat'] : '';
        $language = (isset($headers['language'])) ? $headers['language'] : '';

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $nickname = (isset($params['nickname'])) ? $params['nickname'] : '';
        $trading_pin = (isset($_POST['trade_password'])) ? $_POST['trade_password'] : '';

        $checkNickname = get_user_meta($userID, 'isNickname', true);

        $user_info = get_userdata($user_id);
        $tokens = get_user_meta($user_id, 'user_accesstoken', true);
        $tradePinStatus = get_user_meta($user_id, 'isTradePasswordSet', true);

        if ($uat == '') {
            $output = array('status' => false, 'error_code' => '2101', 'message' => 'token invalid');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($uat != $tokens) {
            $output = array('status' => false, 'error_code' => '2102', 'message' => 'multiple user detected');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($checkNickname != "" && $checkNickname == 'true') {
            $output = array('status' => false, 'error_code' => '1113', 'message' => 'nickname not available');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        if ($tradePinStatus == 'true') {
            $output = array('status' => false, 'error_code' => '1101', 'message' => 'Unable to process');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $getNickname = $wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."usermeta WHERE `meta_key` LIKE 'nickname' AND `meta_value` LIKE '$nickname' ");
        if (count($getNickname) != 0) {
            $output = array('status' => false, 'error_code' => '1113', 'message' => 'nickname not available');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        update_user_meta($user_id, 'duplicate_secret_pin', $trading_pin);
        $hashed_pin = password_hash($trading_pin, PASSWORD_DEFAULT);
        update_user_meta($user_id, 'secret_pin', $hashed_pin);
        update_user_meta($user_id, 'isTradePasswordSet', 'true');


        update_user_meta($user_id, 'isNickname', 'true');
        update_user_meta($user_id, 'nickname', $nickname);

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Nickname completed register!');

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function rejectedImageTmp() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $user_id = $_POST['user_id'];
        update_user_meta($user_id, 'isIdentityVerfied', 'false');
        update_user_meta($user_id, 'identification_status', 'rejected');
        print json_encode(array('status' => true, 'error_code' => '0', 'message' => 'Completed!'));
        die();
    }

    /*     * Function to reset trade password and nickname 
     * params: user_id, token
     */

    public function tradePasswordFalse() {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $user_id = (isset($_GET['user_id'])) ? $_GET['user_id'] : '';
        $adminToken = (isset($_GET['token'])) ? $_GET['token'] : '';

        if ($adminToken != ADMIN_TOKEN) {

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode(array('status' => false, 'error_code' => '100', 'message' => 'Oops, Page not found!'));
            die();
        }

        $user_info = get_userdata($user_id);
        if (count($user_info) == 0) {
            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $uid = $user_info->ID;
        $email = $user_info->data->user_email;

        update_user_meta($uid, 'isTradePasswordSet', 'false');
        update_user_meta($uid, 'isNickname', 'false');
        update_user_meta($uid, 'nickname', '');

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode(array('status' => true, 'error_code' => '0', 'message' => 'Completed!'));
        die();
    }

    public function convertedAddress() {
        date_default_timezone_set(LOCAL_TIMEZONE);
        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $adminToken = (isset($_POST['token'])) ? $_POST['token'] : '';
        $address = (isset($_POST['adrress'])) ? $_POST['adrress'] : '';
        $types = (isset($_POST['types'])) ? $_POST['types'] : '';

        if ($adminToken != ADMIN_TOKEN) {

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode(array('status' => false, 'error_code' => '100', 'message' => 'Oops, Page not found!'));
            die();
        }

        $user_info = get_userdata($user_id);
        if (count($user_info) == 0) {
            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');

            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
            print json_encode($output);
            die();
        }

        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', AUTH_SALT);
        $iv = substr(hash('sha256', SECRET_IV), 0, 16);

        if ($types != '') {
            $storeName = strtolwer($types) . '_address';
            $storeNameDuplicate = strtolwer($types) . '_address_duplicate';
        } else {
            $storeName = 'btc_address';
            $storeNameDuplicate = 'btc_address_duplicate';
        }
        $encryptedprivateKey = base64_encode(openssl_encrypt($address, $encrypt_method, $key, 0, $iv));
        update_user_meta($user_id, $storeName, $encryptedprivateKey);
        update_user_meta($user_id, $storeNameDuplicate, $address);

        $output = array('status' => true, 'error_code' => '0', 'message' => "Converted $address => $encryptedprivateKey");

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    

    public function backend_trade_rates($user_id = null) {
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $this->db = $this->getDb();
        $userdata = $trades = $failedTrades = $tradeRates = $tradeResponse = [];
        $values = [];

        if ($user_id == 0 || $user_id == null) {
            return $values;
        }

        switch (strtoupper($coin_type)) {
            case "BTC":
                $CyrptoWallet = new User_Coin_Wallet();
                break;
            case "JE":
                $CyrptoWallet = new User_Coin_JE_Wallet();
                break;
            case "USDT":
                //$CyrptoWallet = new User_Coin_Wallet();
                break;
            default:
                $CyrptoWallet = new User_Coin_Wallet();
                break;
        }

        $getAllTrades = $CyrptoWallet->count_trade_list($user_id);
        $totalTrade = (count($getAllTrades) != 0) ? $getAllTrades[0]->totalPage : '0';

        $getPendingTrades = $CyrptoWallet->count_trade_list($user_id, 'pending');
        $totalPendingTrade = (count($getPendingTrades) != 0) ? $getPendingTrades[0]->totalPage : '0';

        $getCancelTrades = $CyrptoWallet->count_trade_list($user_id, 'cancelled');
        $totalCancelTrade = (count($getCancelTrades) != 0) ? $getCancelTrades[0]->totalPage : '0';

        $totalTradeRate = ( ( 1 - ( ( $totalPendingTrade + $totalCancelTrade ) / $totalTrade ) ) * 100);


        $values = array(
            'trade' => $totalTrade,
            'trade_rate' => (is_nan($totalTradeRate) === true) ? '0' : $totalTradeRate
        );
        return $values;
    }

    public function sortArray($data, $field) {
        if (!is_array($field))
            $field = array($field);

        usort($data, function($a, $b) use($field) {

            $retval = 0;

            foreach ($field as $fieldname) {

                if ($retval == 0)
                    $retval = strnatcmp($a->$fieldname, $b->$fieldname);
                // if($retval == 0) $retval = strnatcmp($a['$fieldname'],$b['$fieldname']);
            }

            return $retval;
        });

        return array_reverse($data);
    }

    public function convertToUTCDate($date = null) {
        //$date = $_POST['dates'];

        $convertToUTCDate = '';
        $convertingDate = strtotime($date);

        if (date('Y-m-d H:i:s', $convertingDate) == '1970-01-01 00:00:00') {
            return $convertToUTCDate;
        }
        $convertToUTCDate = date('Y-m-d H:i:s', strtotime("-8 hour", $convertingDate));

        return $convertToUTCDate;
    }

    //zxcv//
    public function sendEmail($types, $email, $datas) { //datas can be value/array
        error_log('\n**************** Send Email *****************\n');
        error_log("headers: " . json_encode($this->getallheaders()));
        error_log("data send: $types, $email, " . json_encode($datas));

        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $headers = $this->getallheaders();
        $language = (isset($headers['language'])) ? $headers['language'] : 'English';
        $ip = (isset($headers['ip'])) ? $headers['ip'] : '';
        $browser = (isset($headers['browser'])) ? $headers['browser'] : '';

        if ($email == null || $email == '') {
            return false;
        }

        switch (strtolower($types)) {
            case 'signup':
                $title = 'Signup';
                $bodies = '<tr><td width="260" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br></td></tr>
                            <tr><td style="padding: 25px 0 0 0;">
                                    <p style="text-align: center">These are your verification code</p>
                                    <div style="text-align: center"><span style="padding: 10px 20px 10px 20px;display: inline-block;margin: auto;width: 100px;background-color: #f5a305;border-radius: 99px;color: white;font-size: 20px; font-weight: bold;">' . $datas['otp'] . '</span></div>
                                    <p style="text-align: center;font-size: 12px;">expired on ' . $datas['date'] . '</p>
                                    <br><br>
                                    <p><small>The verification code is only used on the official website of Bitwewe. If it is
                                        not operated by us, please do not enter the verification code anywhere and
                                        modify the login password immediately!<br><br>
                                        To prevent illegal fraud, do not enter your account number, password, and
                                        verification code on any other third-party website other than the official
                                        website.</small>
                                </p></td></tr>
                            <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                            </table></td></tr>';
                break;

            case 'success_register':
                $title = 'Registration Completed';
                $bodies = '<tr><td style="padding:40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr><td width="260" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br></td></tr>
                            <tr><td style="padding: 25px 0 0 0;"><p>Welcome to the trusted digital assets OTC platform.<br>
                                <span style="color:#f0ad4e;font-weight: bold;display: block;padding:10px;margin: 10px 0 10px 0;">You have successfully registered into Bitwewe.</span>
                                Please do not hesitate to contact our customer support if you need any further assistance.<br><br><br>Thank you for choosing Bitwewe.<br></p></td></tr>
                            <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                            </table></td></tr></table>
                            </td></tr>';
                break;

            case 'login':
                $title = 'Login Information';
                $bodies = '<tr><td style="padding: 0px">Hello ' . $datas['username'] . ',<br>We have recorded a login to your account at Bitwewe with the following details:</td>
                            </tr>
                            
                            <tr>
                                <td style="padding: 25px 0 0 0;">
                                <table style="text-align: left; border-collapse: collapse">
                                    <tr><th width="40%" style="border : 1px solid #999999; padding:10px; ">Time</th><td style="border : 1px solid #999999; padding:10px; ">' . date('D, d M Y H:i:s', time()) . '</td></tr>
                                    <tr><th width="40%" style="border : 1px solid #999999; padding:10px; ">IP Address</th><td style="border : 1px solid #999999; padding:10px; "> ' . $ip . ' </td></tr>
                                    <tr><th width="40%" style="border : 1px solid #999999; padding:10px; ">Browser</th><td style="border : 1px solid #999999; padding:10px; "> ' . $browser . ' </td></tr>
                                </table>
                            <br>If this was not you, please secure your email address and add two-factor authentication to it. On Bitwewe, please change your password frequently and set up two-factor authentication. You may contact our customer support if needed.
                            </td></tr>';
                break;

            case 'withdraw':
                $title = 'Withdraw';
                $bodies = '<tr><td width="260" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br></td></tr>
                                <tr>
                                    <td style="padding: 25px 0 0 0;"><p>We have received your request for <span style="color:#f5a305 ">' . $datas['coin_type'] . '</span> withdrawal as followed</p>
                                    <table style="text-align: left; border-collapse: collapse;width: 100%;">
                                        <tr><th width="40%" style="border : 1px solid #999999; padding:10px; ">Amount :</th><td style="border : 1px solid #999999; padding:10px; ">' . $datas['amount'] . ' ' . $datas['coin_type'] . '</td></tr>
                                        <tr><th width="40%" style="border : 1px solid #999999; padding:10px; ">Date/Time :</th><td style="border : 1px solid #999999; padding:10px; ">' . $datas['date'] . '</td></tr>
                                        <tr><th width="40%" style="border : 1px solid #999999; padding:10px; ">withdraw to :</th><td style="border : 1px solid #999999; padding:10px; ">' . $datas['address'] . '</td></tr>
                                    </table>
                                </td></tr>
                            <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr></table></td></tr>';
                break;

            case 'identify':
                $title = 'Verify Identifications';
                $bodies = $datas;
                break;

            case 'appeal':
                $title = 'Login Information';
                $bodies = '<tr><td style="padding:40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td width="260" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr><td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br></td></tr>
                                    <tr><td style="padding: 25px 0 0 0;"><p><span style="color:#f5a305 ">' . $datas['receiverUsername'] . ' </span> marks the order <span style="color:#f5a305 ">' . $datas['order_id'] . ' </span>  as "Appeal" status, please log in toBitwewe account to have a check.</p></td></tr>
                                    <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                                </table></td></tr>
                            </table></td></tr>';
                break;

            case 'cancel':
            case 'cancelled':
                $title = 'Login Information';
                $bodies = '<tr><td style="padding:40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td width="260" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr><td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br></td></tr>
                                    <tr><td style="padding: 25px 0 0 0;"><p><span style="color:#f5a305 ">' . $datas['receiverUsername'] . ' </span> marks the order <span style="color:#f5a305 ">' . $datas['order_id'] . ' </span>  as "Cancel" status, please log in toBitwewe account to have a check.</p></td></tr>
                                    <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                                </table></td></tr>
                            </table></td></tr>';
                break;

            case 'release_coin':
                $title = 'Login Information';
                $bodies = '<tr><td style="padding:40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td width="260" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr><td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br></td></tr>
                                    <tr><td style="padding: 25px 0 0 0;"><p><span style="color:#f5a305 ">' . $datas['receiverUsername'] . ' </span> marks the order <span style="color:#f5a305 ">' . $datas['order_id'] . ' </span>  as "Released Coin" status, please log in toBitwewe account to have a check.</p></td></tr>
                                    <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                                </table></td></tr>
                            </table></td></tr>';
                break;

            case 'payment':
            case 'paid':
                $title = 'Paid Information';
                $bodies = '<tr><td style="padding:40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td width="260" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr><td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br></td></tr>
                                    <tr><td style="padding: 25px 0 0 0;"><p><span style="color:#f5a305 ">' . $datas['receiverUsername'] . ' </span> marks the order <span style="color:#f5a305 ">' . $datas['order_id'] . ' </span>  as "I’ve paid" status, please log in toBitwewe account to have a check.</p></td></tr>
                                    <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                                </table></td></tr>
                            </table></td></tr>';
                break;

            case 'new_order':
                $title = 'New Orders';
                $bodies = '<tr><td style="padding:40px 30px 40px 30px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td width="260" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr><td style="padding: 0px">Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br>
</td></tr>
                                    <tr><td style="padding: 15px 0 0 0;">
                                    <p style="text-align: center">Congratulations!</p><p style="text-align: center">
                                        You\'ve got a new order <span style="color: #f5a305;">' . $datas['order_id'] . '</span>
                                        for <span style="color: #f5a305;">' . $datas['type'] . ' ' . $datas['coin_type'] . '</span> from
                                        <span style="color: #f5a305;">' . $datas['receiverUsername'] . '</span>.</p></td></tr>
                                    <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                                    </table></td></tr></table></td></tr>';
                break;

            case 'deposit':
                $title = 'Deposit Successfully';
                $bodies = '<tr>
        <td style="padding:40px 30px 40px 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="260" valign="top">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="padding: 0px">
                                    Hello <span style="color:#f5a305 ">' . $datas['username'] . '</span>,<br>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 25px 0 0 0;">
                                    <p>Your account has been funded with : </p>
                                    <table style="text-align: left; border-collapse: collapse;width: 100%;">
                                        <tr>
                                            <th width="40%" style="border : 1px solid #999999; padding:10px; ">Amount :</th>
                                            <td style="border : 1px solid #999999; padding:10px; ">' . $datas['amount'] . ' ' . $datas['coin_type'] . '</td>
                                        </tr>
                                        <tr>
                                            <th width="40%" style="border : 1px solid #999999; padding:10px; ">Date/Time :</th>
                                            <td style="border : 1px solid #999999; padding:10px; ">' . $datas['date'] . '</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top:15px;">
                                    <i>Bitwewe Team</i><br>
                                    <a href="https://login.bitwewe.com" style="color:white;text-decoration: unset">
                                        <small>login.bitwewe.com</small>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>';
                break;

            case 'forgotpassword':
                $title = 'Forgot Password';
                $bodies = '<tr><td style="padding:40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr><td width="260" valign="top">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr><td style="padding: 0px">Hello,<br></td></tr>
                                    <tr><td style="padding: 25px 0 0 0;"><p> Reset Password: ' . $datas['links'] . '</p></td></tr>
                                    <tr><td style="padding-top:15px;"><i>Bitwewe Team</i><br><a href="https://login.bitwewe.com" style="color:white;text-decoration: unset"><small>login.bitwewe.com</small></a></td></tr>
                                </table></td></tr>
                            </table></td></tr>';
                break;

            default:
                $title = 'No-Reply Message';
                $bodies = "No Reply Message";
                break;
        }

        $htmlBodies = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns = "http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8"/>
        <meta name = "viewport" content = "width=device-width, initial-scale=1.0"/>
        <style type="text/css">
            @font-face {
            font-family: \'Quicksand\';
            font-style: normal;
            font-weight: 400;
            src: local(\'Quicksand Regular\'), local(\'Quicksand-Regular\'), url(https://fonts.gstatic.com/s/quicksand/v8/6xKtdSZaM9iE8KbpRA_hK1QN.woff2) format(\'woff2\');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
            }
            body,p,h1,h2,a,span {color:white;}
        </style>
    </head>
    <body style = "font-family: \'Quicksand\', sans-serif;color:white;">
        <table align = "center" background = "http://login.bitwewe.com/images/landingpage/background-carousel-2.png" border = "0" cellpadding = "0" cellspacing = "0" width = "600" style = "background-position: top;border: 1px solid #050b2b;box-shadow: 2px 2px 25px 0px #050b2b6e;background-repeat: no-repeat;background-color: #090b2a;background-position: center;">
            
            <tbody>
                <tr>
                    <td align = "center">
                        <img src = "http://login.bitwewe.com/images/Bitwewe-Logo.png" width = "300px" style = "display: block;padding: 50px;"/>
                    </td>
                </tr>
                <tr>
                    <td style="padding:40px 30px 40px 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="260" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    
                    ' . $bodies . '
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor = "#050b2b" style = "padding:10px 5px 10px 5px">
                        <table width = "100%">
                            <tr>
                                <td valign = "top">
                                    <img src = "http://login.bitwewe.com/images/Bitwewe-Favicon.png" alt = "Creating Email Magic"
                                         width = "30px" style = "display: inline-block;"/>
                                    <span style = "vertical-align: top;line-height: 30px;font-size: 12px;letter-spacing:2px; color: white;">Your trusted digital assets OTC platform<br></span>
                                </td>
                                <td align = "right">
                                    <img src = "http://login.bitwewe.com/images/landingpage/icon-insta.png" alt = "Creating Email Magic"
                                         width = "30px" style = "display: inline-block;float:right;margin-right: 5px;"/>
                                    <img src = "http://login.bitwewe.com/images/landingpage/icon-fb.png" alt = "Creating Email Magic"
                                         width = "30px" style = "display: inline-block;float:right;margin-right: 5px;"/>
                                    <img src = "http://login.bitwewe.com/images/landingpage/icon-twitter.png"
                                         alt = "Creating Email Magic" width = "30px"
                                         style = "display: inline-block;float:right;margin-right: 5px;"/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </body>
        </html>';

        $headersSend = 'Content-Type: text/html; charset=UTF-8';
        mail($email, $title, $htmlBodies, $headersSend);

        error_log('\n**************** End Send Email *****************\n');
        return true;
    }

    public function checkingUserAddress() {
        global $wpdb;
        $table_usermeta = $wpdb->prefix . "usermeta";

        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $meta_key = (isset($_POST['meta_key'])) ? $_POST['meta_key'] : '';

        $token = (isset($_POST['token'])) ? $_POST['token'] : '';
        if ($token != ADMIN_TOKEN) {
            $output = array('status' => false, 'error_code' => '99999', 'message' => 'Unauthorized Access, your data will be record and process for further investigation');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        if ($meta_key != '' && $meta_key != null) {
            $getUserMeta = $wpdb->get_results("SELECT *  FROM $table_usermeta WHERE `meta_key` LIKE '%$meta_key%' AND user_id = $user_id");
        } else {
            $getUserMeta = $wpdb->get_results("SELECT *  FROM $table_usermeta WHERE user_id = $user_id");
        }

        $btc_address = get_user_meta($user_id, 'btc_address_duplicate', true);
        $je_address = get_user_meta($user_id, 'je_address_duplicate', true);
        $usdt_address = get_user_meta($user_id, 'usdt_address_duplicate', true);

        $datas = array(
            'BTC' => array(
                'address' => $btc_address
            ),
            'JE' => array(
                'address' => $je_address
            ),
            'USDT' => array(
                'address' => $usdt_address
            ),
        );

        $curl = curl_init();

        if ($btc_address == '') { //zxcv//
            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_BTC . "&newtwork=BTC&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $btc_curl = curl_exec($curl);
            $btc_checkingAddress = json_decode($btc_curl);

            if (strtolower($btc_checkingAddress->status) == "success") {
                $encrypt_method = "AES-256-CBC";
                $key = hash('sha256', AUTH_SALT);
                $iv = substr(hash('sha256', SECRET_IV), 0, 16);

                $btc_encryptedprivateKey = base64_encode(openssl_encrypt($btc_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                $datas['BTC']['address'] = $btc_checkingAddress->data->address;
                $datas['BTC']['address_key'] = $btc_encryptedprivateKey;
                update_user_meta($user_id, 'btc_address', $btc_encryptedprivateKey);
                update_user_meta($user_id, 'btc_address_duplicate', $btc_checkingAddress->data->address);
            }
        }

        if ($je_address == '') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_JE . "&newtwork=ETH&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $je_curl = curl_exec($curl);
            $je_checkingAddress = json_decode($je_curl);
            if (strtolower($je_checkingAddress->status) == "success") {
                $encrypt_method = "AES-256-CBC";
                $key = hash('sha256', AUTH_SALT);
                $iv = substr(hash('sha256', SECRET_IV), 0, 16);

                $je_encryptedprivateKey = base64_encode(openssl_encrypt($je_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                $datas['JE']['address'] = $je_checkingAddress->data->address;
                $datas['JE']['address_key'] = $je_encryptedprivateKey;
                update_user_meta($user_id, 'je_address', $je_encryptedprivateKey);
                update_user_meta($user_id, 'je_address_duplicate', $je_checkingAddress->data->address);
            }
        }

        if ($usdt_address == '') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => JONVI_LINK . "?api_key=" . API_JE . "&newtwork=USDT&label=$user_id",
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'My-Wordpress-Observer',
                CURLOPT_SSL_VERIFYPEER => False,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17',
                CURLOPT_HTTPHEADER => array('Accept: application/json'),
                CURLOPT_POST => false
            ));
            $usdt_curl = curl_exec($curl);
            $usdt_checkingAddress = json_decode($usdt_curl);
            if (strtolower($usdt_checkingAddress->status) == "success") {
                $encrypt_method = "AES-256-CBC";
                $key = hash('sha256', AUTH_SALT);
                $iv = substr(hash('sha256', SECRET_IV), 0, 16);

                $usdt_encryptedprivateKey = base64_encode(openssl_encrypt($usdt_checkingAddress->data->address, $encrypt_method, $key, 0, $iv));
                $datas['USDT']['address'] = $usdt_checkingAddress->data->address;
                $datas['USDT']['address_key'] = $usdt_encryptedprivateKey;
                update_user_meta($user_id, 'usdt_address', $usdt_encryptedprivateKey);
                update_user_meta($user_id, 'usdt_address_duplicate', $usdt_checkingAddress->data->address);
            }
        }

        curl_close($curl);

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Successfully');
        $userdata = array('datas' => $datas);
        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');

        print json_encode($output);
        die();
    }

    public function checkUserBalance() { //zxcv//
        global $wpdb;
        date_default_timezone_set(LOCAL_TIMEZONE);
        $headers = $this->getallheaders();
        $clients = $api_modules = '';

        $tokens = (isset($_GET['token'])) ? $_GET['token'] : '';
        $task = (isset($_GET['task'])) ? $_GET['task'] : '';
        $email = (isset($_GET['email'])) ? $_GET['email'] : '';
        $address = (isset($_GET['address'])) ? $_GET['address'] : '';

        $BTC_Wallet = new User_Coin_Wallet();
        $Je_Wallet = new User_Coin_JE_Wallet();
        $USDT_Wallet = new User_Coin_USDT_Wallet();

        if ($tokens == '') {
            $output = array('status' => false, 'error_code' => '99999', 'message' => 'Unauthorized Access, your data will be record and process for further investigation');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        switch ($tokens) {
            case JONVI_ACCESS:
                $clients = "jonvi";
                $users = $wpdb->get_results("SELECT *  FROM `" . $wpdb->prefix . "users` where ID = (SELECT user_id FROM `" . $wpdb->prefix . "usermeta` WHERE `meta_value` LIKE '$address')");
                if (count($users) == 0 || empty($users)) {
                    $users = $wpdb->get_results("SELECT *  FROM `" . $wpdb->prefix . "usermeta` WHERE `meta_key` LIKE 'email_duplicate' AND `meta_value` LIKE '$email'");
                }
                break;

            case ADMIN_TOKEN:
                $clients = "admin";
                $users = $wpdb->get_results("SELECT *  FROM `" . $wpdb->prefix . "users` where ID = (SELECT user_id FROM `" . $wpdb->prefix . "usermeta` WHERE `meta_value` LIKE '$address')");
                if (count($users) == 0 || empty($users)) {
                    $users = $wpdb->get_results("SELECT *  FROM `" . $wpdb->prefix . "users` WHERE `user_email` LIKE '%$email%'");
                    if (count($users) == 0 || empty($users)) {
                        $users = $wpdb->get_results("SELECT *  FROM `" . $wpdb->prefix . "usermeta` WHERE `meta_key` LIKE 'email_duplicate' AND `meta_value` LIKE '$email'");
                    }
                }
                break;


            default:
                break;
        }

        if (count($users) == 0 || empty($users)) {
            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        $user_info = $users[0];

        if ($clients != "admin") {
            if (strpos($user_info->user_email, $clients) === false) {
                $output = array('status' => false, 'error_code' => '99999', 'message' => 'Unauthorized Access, your data will be record and process for further investigation');
                $client_log = $wpdb->insert($wpdb->prefix . 'client_access_logs', array('token' => $tokens, 'client' => $clients, 'api_modules' => $task, 'request' => json_encode($output)));
                header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
                header('Content-type: application/json');
                header('Access-Control-Allow-Origin: *');

                print json_encode($output);
                die();
            }
        }

        $output = array('status' => true, 'error_code' => '0', 'message' => 'Valid user');
        $BTC_Wallet = new User_Coin_Wallet();
        $JE_Wallet = new User_Coin_JE_Wallet();
        $USDT_Wallet = new User_Coin_USDT_Wallet();

        $histories = $relaseCoin_History = [];

        $BTC_History = $BTC_histories = $BTC_Types = [];
        $BTC_History = $BTC_Wallet->get_account_histories($user_info->ID);
        $BTC_ReleaseCoin_History = $BTC_Wallet->release_coin_history($user_info->ID);
        if (count($BTC_ReleaseCoin_History) != 0) {
            foreach ($BTC_ReleaseCoin_History as $key => $rch) {
                $relaseCoin_History[$key] = array(
                    'id' => $rch->id,
                    'coin_type' => 'btc',
                    'amount' => $rch->amount,
                    'refid' => '0',
                    'post_type' => "release coin",
                    'date' => $this->convertToUTCDate($rch->created)
                );
            }
        }
        if (count($BTC_History) != 0) {
            foreach ($BTC_History as $history) {
                switch ($history->type) {
                    case '1':
                        $BTC_Types[$history->id] = 'deposit';
                        break;
                    case '2':
                        $BTC_Types[$history->id] = 'post ads [sell]';
                        break;
                    case '3':
                        $BTC_Types[$history->id] = 'withdraw';
                        break;
                    case '4':
                        $BTC_Types[$history->id] = 'received coin';
                        break;
                    case '5':
                        $BTC_Types[$history->id] = 'post ads [buy]';
                        break;
                    case '6':
                        $BTC_Types[$history->id] = 'release coin';
                        break;
                    case '7':
                        $BTC_Types[$history->id] = 'editing post ads [increment]';
                        break;
                    case '8':
                        $BTC_Types[$history->id] = 'editing post ads [decrement]';
                        break;

                    default:
                        $BTC_Types[$history->id] = '';
                        break;
                }
                if ($history->type != '5') {
                    $BTC_histories[$history->id] = array(
                        'id' => $history->id,
                        'coin_type' => 'btc',
                        'amount' => $history->amount,
                        'refid' => $history->refid,
                        'post_type' => $BTC_Types[$history->id],
                        'date' => $this->convertToUTCDate($history->datetime)
                    );
                }
            }
            $BTC_histories = array_merge($BTC_histories, $relaseCoin_History);
            $histories = $this->resortingArray($histories, $BTC_histories);
        }


        $JE_History = $JE_histories = $JE_Types = [];
        $JE_History = $JE_Wallet->get_account_histories($user_info->ID);
        $JE_ReleaseCoin_History = $JE_Wallet->release_coin_history($user_info->ID);

        if (count($JE_ReleaseCoin_History) != 0) {
            foreach ($JE_ReleaseCoin_History as $key => $rch) {
                $relaseCoin_History[$key] = array(
                    'id' => $rch->id,
                    'coin_type' => 'je',
                    'amount' => $rch->amount,
                    'refid' => '0',
                    'post_type' => "release coin",
                    'date' => $this->convertToUTCDate($rch->created)
                );
            }
        }


        if (count($JE_History) != 0) {
            foreach ($JE_History as $history) {
                switch ($history->type) {
                    case '1':
                        $JE_Types[$history->id] = 'deposit';
                        break;
                    case '2':
                        $JE_Types[$history->id] = 'post ads [sell]';
                        break;
                    case '3':
                        $JE_Types[$history->id] = 'withdraw';
                        break;
                    case '4':
                        $JE_Types[$history->id] = 'received coin';
                        break;
                    case '5':
                        $JE_Types[$history->id] = 'post ads [buy]';
                        break;
                    case '6':
                        $JE_Types[$history->id] = 'release coin';
                        break;
                    case '7':
                        $JE_Types[$history->id] = 'editing post ads [increment]';
                        break;
                    case '8':
                        $JE_Types[$history->id] = 'editing post ads [decrement]';
                        break;

                    default:
                        $JE_Types[$history->id] = '';
                        break;
                }
                if ($history->type != '5') {
                    $JE_histories[$history->id] = array(
                        'id' => $history->id,
                        'coin_type' => 'je',
                        'amount' => $history->amount,
                        'refid' => $history->refid,
                        'post_type' => $JE_Types[$history->id],
                        'date' => $this->convertToUTCDate($history->datetime)
                    );
                }
            }
            $JE_histories = array_merge($JE_histories, $relaseCoin_History);
            $histories = $this->resortingArray($histories, $JE_histories);
        }


        $USDT_History = $USDT_histories = $USDT_Types = [];
        $USDT_History = $USDT_Wallet->get_account_histories($user_info->ID);
        $USDT_ReleaseCoin_History = $USDT_Wallet->release_coin_history($user_info->ID);

        if (count($USDT_ReleaseCoin_History) != 0) {
            foreach ($USDT_ReleaseCoin_History as $key => $rch) {
                $relaseCoin_History[$key] = array(
                    'id' => $rch->id,
                    'coin_type' => 'usdt',
                    'amount' => $rch->amount,
                    'refid' => '0',
                    'post_type' => "release coin",
                    'date' => $this->convertToUTCDate($rch->created)
                );
            }
        }
        if (count($USDT_History) != 0) {
            foreach ($USDT_History as $history) {
                switch ($history->type) {
                    case '1':
                        $USDT_Types[$history->id] = 'deposit';
                        break;
                    case '2':
                        $USDT_Types[$history->id] = 'post ads [sell]';
                        break;
                    case '3':
                        $USDT_Types[$history->id] = 'withdraw';
                        break;
                    case '4':
                        $USDT_Types[$history->id] = 'received coin';
                        break;
                    case '5':
                        $USDT_Types[$history->id] = 'post ads [buy]';
                        break;
                    case '6':
                        $USDT_Types[$history->id] = 'release coin';
                        break;
                    case '7':
                        $USDT_Types[$history->id] = 'editing post ads [increment]';
                        break;
                    case '8':
                        $USDT_Types[$history->id] = 'editing post ads [decrement]';
                        break;

                    default:
                        $USDT_Types[$history->id] = '';
                        break;
                }
                if ($history->type != '5') {
                    $USDT_histories[$history->id] = array(
                        'id' => $history->id,
                        'coin_type' => 'usdt',
                        'amount' => $history->amount,
                        'refid' => $history->refid,
                        'post_type' => $USDT_Types[$history->id],
                        'date' => $this->convertToUTCDate($history->datetime)
                    );
                }
            }
            $USDT_histories = array_merge($USDT_histories, $relaseCoin_History);
            $histories = $this->resortingArray($histories, $USDT_histories);
        }

        $orders = array(
            'btc' => $BTC_Wallet->get_trade_list($user_info->ID),
            'je' => $JE_Wallet->get_trade_list($user_info->ID),
            'usdt' => $USDT_Wallet->get_trade_list($user_info->ID),
        );

        $userdata = array(
            'data' => array(
                'btc' => array(
                    'btc_available' => $BTC_Wallet->get_user_wallet_available_balance($user_info->ID), //asdf//
                    'btc_freeze' => number_format($BTC_Wallet->get_user_wallet_freeze_balanced($user_info->ID), 8, '.', ''), //asdf//
                    'coin_address' => get_user_meta($user_info->ID, 'btc_address_duplicate', true)
                ),
                'je' => array(
                    'je_available' => $Je_Wallet->get_user_wallet_available_balance($user_info->ID),
                    'je_freeze' => $Je_Wallet->get_user_wallet_freeze_balanced($user_info->ID),
                    'coin_address' => get_user_meta($user_info->ID, 'je_address_duplicate', true)
                ),
                'usdt' => array(
                    'usdt_available' => $USDT_Wallet->get_user_wallet_available_balance($user_info->ID),
                    'usdt_freeze' => $USDT_Wallet->get_user_wallet_freeze_balanced($user_info->ID),
                    'coin_address' => get_user_meta($user_info->ID, 'usdt_address_duplicate', true)
                ),
                'orders' => $orders,
                'histories' => $histories
            )
        );

        $client_log = $wpdb->insert($wpdb->prefix . 'client_access_logs', array('token' => $tokens, 'client' => $clients, 'api_modules' => $task, 'request' => json_encode($userdata)));

        $output = array_merge($output, $userdata);

        header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        print json_encode($output);
        die();
    }

    public function adminChecking() {
        global $wpdb;
        $table_users = $wpdb->prefix . "users";
        $table_usermeta = $wpdb->prefix . "usermeta";
        $getUserMeta = [];

        $token = (isset($_POST['token'])) ? $_POST['token'] : '';
        if ($token != ADMIN_TOKEN) {
            $output = array('status' => false, 'error_code' => '99999', 'message' => 'Unauthorized Access, your data will be record and process for further investigation');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');

            print json_encode($output);
            die();
        }

        $getUsers = $wpdb->get_results("SELECT * FROM $table_users");
        if (count($getUsers) != 0) {
            foreach ($getUsers as $users) {
                echo "user: $users->ID \n";
                //echo "SELECT user_id, meta_value  FROM $table_usermeta WHERE `meta_key` LIKE '%je_address_duplicate%' AND user_id = $users->ID \n";
                $getUserMeta[$users->ID] = $wpdb->get_results("SELECT user_id, meta_value  FROM $table_usermeta WHERE `meta_key` LIKE '%je_address_duplicate%' AND user_id = $users->ID");
                print_r($getUserMeta[$users->ID]);
                echo "\n\n";
            }
        }

        die();
    }
    
    public function withdrawProcess() {
        global $wpdb;
        $user_id = (isset($_POST['user_id']))? $_POST['user_id'] : $_GET['user_id'];
        $request_id = (isset($_POST['request_id']))? $_POST['request_id'] : $_GET['request_id'];
        $sql = "UPDATE `wp_je_wallet_request_task` SET status='cancelled' WHERE id = '$request_id' AND `type` LIKE '3'";
        $updatedSQL = $wpdb->query($sql);
        if($updatedSQL){
            $output = array('status' => true, 'error_code' => '0', 'message' => 'Updated successfully!');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
        } else {
            $output = array('status' => false, 'error_code' => '1112', 'message' => 'Invalid input data');
            header('Access-Control-Allow-Headers: access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, X-Requested-With, Content-Type, viewerTZOffset, deviceid, mobile, session, uat, appversion, ip, language, browser');
            header('Content-type: application/json');
            header('Access-Control-Allow-Origin: *');
        }
        die();
    }

//func ends her
}

// class end 
