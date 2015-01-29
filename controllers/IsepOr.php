<?php
class IsepOr_Controller extends Controller {
    
    public function firstRound() {
        $this->setView('firstRound.php');

        if (!isset(User_Model::$auth_data))
            throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
        if (!isset(User_Model::$auth_data['student_number']))
            throw new Exception('You must be a student to see this');
        /* if(Cache::read('IsepOrRound') !== 1 && User_Model::$auth_data['admin'] != '1')
            throw new Exception('It\'s not ready for Prime Time'); */
        if($this->model->checkVote(User_Model::$auth_data['id'], 1) > 0){
            $this->set('empty_post', false);
            return;
        }
        
        try {
            if(empty($_POST)){
                if(User_Model::$auth_data['admin'] == '1')
                    Cache::delete('IsepOrQuestions');
                if(!($questions = Cache::read('IsepOrQuestions'))){
                    $questions = $this->model->fetchQuestions();
                    Cache::write('IsepOrQuestions', $questions, 11250);
                }
                $this->set(array(
                    'questions'  => $questions,
                    'empty_post' => true
                ));
            } else {
                $this->model->save($_POST, 1);
                $this->set('empty_post', false);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return true;
    }
    
    public function finalRound() {
        $this->setView('finalRound.php');

        if (!isset(User_Model::$auth_data))
            throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
        if (!isset(User_Model::$auth_data['student_number']))
            throw new Exception('You must be a student to see this');
     /*   if(Cache::read('IsepOrRound') != 2 && User_Model::$auth_data['admin'] != '1')
            throw new Exception('It\'s not ready for Prime Time');*/
        if($this->model->checkVote(User_Model::$auth_data['id'], 2) > 0){
            $this->set('empty_post', false);
            return;
        }

        try {
            if(empty($_POST)){
                if(User_Model::$auth_data['admin'] == '1'){
                    Cache::delete('IsepOrFinals');
                    Cache::delete('IsepOrQuestions');
                }
                if(!($questions = Cache::read('IsepOrQuestions'))){
                    $questions = $this->model->fetchQuestions();
                    Cache::write('IsepOrQuestions', $questions, 11250);
                }

                if(!($finalList = Cache::read('IsepOrFinals'))){
                    foreach($questions as $value){
                        if(strpos($value['type'], ',')){
                            $data = array();
                            foreach(explode(',', $value['type']) as $type){
                               $data = self::__array_rePad($data, $this->model->fetchFinals($value['id'], $type, 1,true));
                            }
                            $finalList[$value['id']] = array_slice(self::__array_orderby($data, 'cmpt', SORT_DESC), 0, 3);
                        } else 
                            $finalList[$value['id']] = $this->model->fetchFinals($value['id'], $value['type'], 1,true);
                    }
                    Cache::write('IsepOrFinals', $finalList, 11250);
                }
                $this->set(array(
                    'empty_post' => true,
                    'datas' => $finalList,
                    'questions' => $questions,
                ));
            } else {
                $this->model->save($_POST, 2);
                $this->set('empty_post', false);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return true;
    }
    
    public function result() {
        if (!isset(User_Model::$auth_data))
            throw new ActionException('User', 'signin', array('redirect' => $_SERVER['REQUEST_URI']));
        if (!isset(User_Model::$auth_data['student_number']))
            throw new Exception('You must be a student to see this');		
		
        if(Cache::read('IsepOrRound') !== 3 && User_Model::$auth_data['admin'] != '1')
            throw new Exception('It\'s not ready for Prime Time');
		
		$this->setView('result.php');
		
        if(User_Model::$auth_data['admin'] == '1') {
            Cache::delete('IsepOrQuestions');
            Cache::delete('IsepOrCount');
            Cache::delete('IsepOrResults');
        }
        if(!($questions = Cache::read('IsepOrQuestions'))){
            $questions = $this->model->fetchQuestions();
            Cache::write('IsepOrQuestions', $questions, 11250);
        }
        if (!($finalList = Cache::read('IsepOrResults'))) {
            foreach ($questions as $value) {
                if (strpos($value['type'], ',')) {
                    $data = array();
                    foreach (explode(',', $value['type']) as $type) {
                        $data = self::__array_rePad($data, $this->model->fetchFinals($value['id'], $type, 2));
                    }
                    $finalList[$value['id']] = array_slice(self::__array_orderby($data, 'cmpt', SORT_DESC), 0, 3);
                } else
                    $finalList[$value['id']] = $this->model->fetchFinals($value['id'], $value['type'], 2);
            }
            Cache::write('IsepOrResults', $finalList, 11250);
        }

        if(!($count = Cache::read('IsepOrCount'))){
            $count = array();
            foreach (($this->model->countUser()) as $value) {
                $count[$value['isepdor_questions_id']] = $value['Lignes'];
            }
            Cache::write('IsepOrCount', $count, 11250);
        }

        $this->set(array(
                'countUser' => $count,
                'datas'     => $finalList,
                'questions' => $questions,
        ));
    }
    
    /**
     * Isep d'Or Special Autocomplete
     */
    public function IsepOrAutocomplete(){
        $this->setView('autocomplete.php');

        if (!isset(User_Model::$auth_data))
            throw new Exception('You must be logged in');
        if (!isset(User_Model::$auth_data['student_number']))
            throw new Exception('You must be a student to see this');
        if(!isset($_GET['q']) && !isset($_GET['type']))
            throw new Exception('Query parameter "q" or "type" not set');

        try {
            $limit = isset($_GET['limit']) && ctype_digit($_GET['limit']) ? (int) $_GET['limit'] : 10;
            $data = array();
            foreach(explode(',', $_GET['type']) as $value){
                switch ($value) {
                    case 'events':
                        $extra = (!empty($_GET['extra'])) ? $_GET['extra'] : null;
                        $data = self::__array_rePad((array) $data,(array) $this->model->searchEvents($_GET['q'], $limit, $extra));
                        break;
                    case 'students':
                        $promo = (!empty($_GET['extra'])) ? (int) $_GET['extra'] : null;
                        $data = self::__array_rePad((array) $data,(array) $this->model->searchUsers($_GET['q'], $limit, $promo));
                        break;
                    case 'associations':
                        $extra = (!empty($_GET['extra'])) ? $_GET['extra'] : null;
                        $data = self::__array_rePad((array) $data,(array) $this->model->searchAssociations($_GET['q'], $limit, $extra));
                        break;
                    case 'employees':
                        $extra = (!empty($_GET['extra'])) ? $_GET['extra'] : null;
                        $data = self::__array_rePad((array) $data,(array) $this->model->searchEmployees($_GET['q'], $limit, $extra));
                        break;
                    default:
                        throw new Exception('Error, wrong type of input');
                        break;
                }
            }
            $this->set(array(
                'data'	=> $data
            ));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    
    static public function __array_rePad(array $array, array $array2) {
       $array = array_values($array);
       $array2 = array_values($array2);
       $nb = count($array)+1;
       foreach ($array2 as $key => $value) {
           $array[$key+$nb] = $value;
       }
       return array_values($array);
    }
    
    static public function __array_orderby() {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        //$args[] = &$data;
        //call_user_func_array('array_multisort', $args);
        array_multisort($args[0], $args[1], $data);
        return $data;
    }
}
