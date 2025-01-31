<?php
 
namespace core\chat;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// Чат
class Chat implements MessageComponentInterface {
    private $clients; // хранение всех подключенных пользователей
    private $usersTable; // таблица пользователей
    private $connectionsTable; // таблица подключений
   
    public function __construct($usersTable, $connectionsTable) {
        $this->clients = new \SplObjectStorage;
        $this->user = $user;
        $this->usersTable = $usersTable;
        $this->connectionsTable = $connectionsTable;
        $this->connectionsTable->clearConnections();
    }
   
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn); // добавление нового пользователя
        $message = json_encode([ 'onсonnection' => $conn->resourceId ]);
        foreach ($this->clients as $client) $client->send($message); // рассылаем пользователям сообщение 
    }
    
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn); // Отключаем клиента
        $userEmail = $this->connectionsTable->getConnectionUserEmail( $conn->resourceId );
        $this->connectionsTable->removeConnection( $conn->resourceId );

        echo "OFF_CONNECTION $userEmail\n";
        $message = json_encode([ 'offсonnection' => $userEmail ]);
        foreach ($this->clients as $client) $client->send($message);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // после соединения пользователь отправляет пакет с id подключения и именем. Данные записываются в БД
        $data = json_decode($msg);
        if($data->messageOnconnection){
            $isAdded = $this->connectionsTable->addConnection( ['author'=>$data->author, 'userId'=>$data->userId] );
            $author = trim($data->author);
            switch($isAdded){
                case 0:
                    echo "CONNECTION $author: ошибка\n";
                    break;
                case 1:
                    echo "CONNECTION $author: добавлено\n";
                    break;
                case 2:
                    echo "CONNECTION $author: уже существует\n";
                    break;
                default:
                    echo "CONNECTION $author: не существует в БД\n";
            }
        }
        // сообщение пользователя
        else{
            echo "$msg\r\n";
        }
        foreach ($this->clients as $client) $client->send($msg);
    }

    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "ОШИБКА: {$e->getMessage()}\r\n";
        $conn->close();
    }
}