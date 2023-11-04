<?php
class DB
{
    private $db;
    public function __construct()
    {
        $config=(require __DIR__ . '/../.env')['db'];
        $dsn = "mysql:host={$config['host']};dbname={$config['db']}";
        $this->db = new PDO($dsn, 'root','root') or die('подключение к базе данных не выполнено');
    }
    private function parse_params_key($params){

        if($params){
            $parse="";
            $count=0;
            foreach ($params as $param=>$item){
                    $parse .= "$item[key]$item[symb]:$item[key]";
                    $count++;
                    if(count($params)!=$count){
                        $parse.=' AND ';
                    }
            }
        } else $parse=1;
        return $parse;
    }
    private function parse_params_value($params){

        if($params){
            $parse=[];
            foreach ($params as $param=>$item){
                $parse[":$item[key]"]= "$item[value]";
            }
        } else $parse=1;
        return $parse;
    }
    private function parse_set($set){

        if($set){
            $parse='';
            foreach ($set as $key=>$item){
                $parse.="$key=:$key";
            }
        } else $parse=1;
        return $parse;
    }

    public function select($table=null,$columns='*',$params=[]){
        $parse_key=$this->parse_params_key($params);
        $parse_value =$this->parse_params_value($params);
        $sql = "SELECT {$columns} FROM {$table} WHERE {$parse_key}";
        $query = $this->db->prepare($sql);

        if(!$params)
            $query->execute();
        else
            $query->execute($parse_value);

        $arr = $query->fetchAll(PDO::FETCH_ASSOC);
        return $arr;
    }
    public function insert($table =null,$columns=null, $values=null){
        if($values!=null && $columns!=null){
            $columns=implode(",", $columns);
            $values="'".implode("','", $values)."'";//дополнительные ковычки
        }
        $sql = "INSERT INTO {$table}({$columns}) VALUES ({$values})";
        $query = $this->db->prepare($sql);
        $query->execute();
        $result = $query->rowCount();
        return $result;
    }
    public function select_rand_value($table=null,$columns='*'){
        $sql = "SELECT {$columns} FROM {$table} ORDER BY RAND() LIMIT 1";
        $query = $this->db->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function update($table, $params=null, $set=null){
        $parse_key=$this->parse_params_key($params);
        $parse_value =$this->parse_params_value($params);
        $parse_set =$this->parse_set($set);

        $sql = "UPDATE {$table} SET {$parse_set} WHERE {$parse_key}";
        $query = $this->db->prepare($sql);

        if (empty($params))
            $query->execute($set);
        elseif (empty($set))
            $query->execute($parse_value);
        else
            $query->execute(array_merge($set,$parse_value));
        $result = $query->rowCount();
        return $result;
    }
    public function delete($table, $params){
        $parse_key=$this->parse_params_key($params);
        $parse_value =$this->parse_params_value($params);
        $sql = "DELETE FROM {$table} WHERE {$parse_key}";
        $query = $this->db->prepare($sql);
        $query->execute($parse_value);
    }
    public function select_not_used_events($table=null,$columns='*',$unix,$used){
        $sql = "SELECT {$columns} FROM {$table} WHERE unix<:unix AND used=:used";
        $query = $this->db->prepare($sql);
        $query->execute([':unix'=>$unix,':used'=>$used]);
        $arr = $query->fetchAll(PDO::FETCH_ASSOC);
        return $arr;
    }
}