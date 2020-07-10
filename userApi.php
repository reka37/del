<?php
require_once 'api.php';

class UsersApi extends Api
{
	/**
	*   Метод POST
	*   Метод SessionSubscribe
	*	Записаться на сессию (лекцию). 
	*	Каждая сессия имеет ограниченное число мест. 
	*	Записаться может только пользователь, который есть в таблице Participant.
	*   http://server/api/sessionSubscribe
	*   @return string
	*/
    public function sessionSubscribeAction()
    {
		$jsonRaw = file_get_contents('php://input');
		$json = json_decode($jsonRaw);	
		$result = $this->putSubscribe($json->sessionId, $json->userEmail);
		$answer = [
			"status"=> "OK", 
			"payload"=> $result, 
			"message"=> "Спасибо за пользование нашим сервисом!!!" 
		];
		return $this->response((object)$answer, 200);
    }
	
    /**
	* Записать в базу
     * @return boolean
     */
	public function putSubscribe($sessionId, $userEmail)
	{
		$query = "SELECT * FROM Participant WHERE Email = '$userEmail'";
		$dbh = new PDO('mysql:host=localhost;dbname=database', 'root', '');
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		$items = array();
		$row = $stmt->fetch(PDO::FETCH_OBJ);
		
		if ($row) {
			$query = 'INSERT INTO Speaker(`name`) VALUES (:name)';
			$stmt = $dbh->prepare($query);
			$stmt->bindValue(':name',$row->Name, PDO::PARAM_STR);
			if ($stmt->execute()) {
				return true;
			} else {
				return false; 
			}
		}
	}

	/**
	* Метод POST
	* Метод “Table”
	* Получить данные таблицы. 
	* Метод возвращает все имеющиеся данные для указанной таблицы (из числа разрешенных). 
	* Важно: количество доступных таблиц в дальнейшем планируется значительно увеличить.
	*  http://server/api/table
	* @return string
	*/
    public function tableAction()
    {	
		$jsonRaw = file_get_contents('php://input');
		$json = json_decode($jsonRaw);		
		$tables = [
			'News', 
			'Session'
		];		
		if (in_array($json->table, $tables)) {
			if (!empty($json->id)) {
				$result = $this->getOneInfo($json->table, $json->id);
			} else {
				$result = $this->getAllInfo($json->table);
			}
		}	
		$answer = [
			"status"	=> "OK", 
			"payload"	=> $result, 
			"message"	=> "Спасибо за пользование нашим сервисом!!!" 
		];
		return $this->response((object)$answer, 200);
    }
	  /**
     * Выбрать все записи
     * @return string
     */
    public function getAllInfo($tableName)
    {		
		$query = "SELECT * FROM " . $tableName;
		$dbh = new PDO('mysql:host=localhost;dbname=database', 'root', '');
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		$items = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {	
			$items[] = $row; 
		}			
		return $items;
    }
	
	  /**
     * Выбрать одну запись (по ее id)
     * @return string
     */
    public function getOneInfo($tableName, $id)
    {		
		$query = "SELECT * FROM " . $tableName . " WHERE ID = " . $id;
		$dbh = new PDO('mysql:host=localhost;dbname=database', 'root', '');
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		$items = array();
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {	
			$items[] = (object)$row; 
		}			
		return $items;
    }
}