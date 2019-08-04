<?php
/**
 * Created by PhpStorm.
 * User: Willian Correa (willianoliveirac96@gmail.com)
 * Date: 8/3/2019
 * Time: 12:39 PM
 */

/**
 * Classe responsável pela interação com o banco de dados.
 *
 * Class DB
 */
class DB {

    /**
     * Host/Endereço do banco de dados
     *
     * @var string $hostname
     */
    private $hostname;

    /**
     * Usuário do banco dados
     *
     * @var string $username
     */
    private $username;

    /**
     * Senha do usuário do banco de dados
     *
     * @var string $password
     */
    private $password;

    /**
     * Nome da base usada no banco de dados
     *
     * @var string $database
     */
    private $database;

    /**
     * @var mysqli $mysqli
     */
    public $mysqli;

    /**
     * DB constructor, recebe os dados de conexão do banco de dados e tenta fazer a conexão.
     *
     * @param string $hostname endereço de conexão do banco de dados
     * @param string $username username a ser utilizado na conexão com o banco de dados
     * @param string $password senha do usuário a ser utilizada na conexão com o banco de dados
     * @param string $database base que será utilizada na conexão com o banco de dados
     *
     * @return void
     */
    public function __construct($hostname, $username, $password, $database)
    {
        $this->setHostname($hostname);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setDatabase($database);

        $this->connect();
    }

    /**
     * Seta o endereço de conexão do banco de dados
     *
     * @param string $hostname
     * @return void
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * Seta o username a ser utilizado na conexão com o banco de dados
     *
     * @param string $username
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Seta a senha do usuário a ser utilizada na conexão com o banco de dados
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Seta a base que será utilizada na conexão com o banco de dados
     *
     * @param string $dabatase
     * @return void
     */
    public function setDatabase($dabatase)
    {
        $this->database = $dabatase;
    }

    /**
     * Tenta conectar com o banco de dados, se não houver conexão, o script morre com a mensagem de erro.
     *
     * @return void
     */
    private function connect()
    {
        $mysqli = new mysqli($this->hostname, $this->username, $this->password, $this->database);

        if ($mysqli->connect_error) {
            die('Erro ao conectar ao banco de dados: '. $mysqli->connect_errno . ': ' . $mysqli->connect_error);
        }

        $this->mysqli = $mysqli;
    }

    /**
     * Executa uma query no banco de dados e volta as linhas afetadas.
     *
     * @param string $sql comando SQL para ser executado.
     * @return array resultado do sql
     */
    public function query($sql)
    {

        //$sql = mysqli_real_escape_string($this->mysqli, $sql);

        $query = $this->mysqli->query($sql);
        
        $rows = $query->fetch_all(MYSQLI_ASSOC);

        return $rows;
    }

    /**
     * Retorna a primeira linha do SQL executado.
     *
     * @param string $sql comando SQL para ser executado.
     * @throws Exception se voltar mais de uma linha
     * @return array retorna um array associativo com o resultado da query
     */
    public function queryFirstRow($sql)
    {
        $result = $this->query($sql);

        if(count($result)>1) {
            throw new Exception('QUERY executada voltou mais de uma linha.');
        }

        return $result[0];
    }
}