<?php
class PatientRepository{private mysqli $db;public function __construct(){$this->db=Database::getConnection();}
public function search(string $q='',string $sort='id',string $dir='DESC',int $limit=10,int $offset=0):array{$allowed=['id','name','dob'];if(!in_array($sort,$allowed,true)){$sort='id';}$dir=strtoupper($dir)==='ASC'?'ASC':'DESC';$rows=[];if($q!==''){$sql="SELECT id,name,gender,dob,phone FROM patients WHERE deleted_at IS NULL AND name LIKE ? ORDER BY $sort $dir LIMIT ? OFFSET ?";$st=$this->db->prepare($sql);$like='%'.$q.'%';$st->bind_param('sii',$like,$limit,$offset);}else{$sql="SELECT id,name,gender,dob,phone FROM patients WHERE deleted_at IS NULL ORDER BY $sort $dir LIMIT ? OFFSET ?";$st=$this->db->prepare($sql);$st->bind_param('ii',$limit,$offset);} $st->execute();$res=$st->get_result();while($r=$res->fetch_assoc()){$rows[]=$r;}$st->close();return $rows;}

public function count(string $q=''):int{if($q!==''){$st=$this->db->prepare('SELECT COUNT(*) AS c FROM patients WHERE deleted_at IS NULL AND name LIKE ?');$like='%'.$q.'%';$st->bind_param('s',$like);}else{$st=$this->db->prepare('SELECT COUNT(*) AS c FROM patients WHERE deleted_at IS NULL');}$st->execute();$res=$st->get_result();$c=(int)($res->fetch_assoc()['c']??0);$st->close();return $c;}

public function findById(int $id):?array{$st=$this->db->prepare('SELECT * FROM patients WHERE id=? AND deleted_at IS NULL');$st->bind_param('i',$id);$st->execute();$res=$st->get_result();$row=$res->fetch_assoc();$st->close();return $row?:null;}

public function create(array $d):int{$st=$this->db->prepare('INSERT INTO patients (name,gender,dob,phone,address) VALUES (?,?,?,?,?)');$name=$d['name'];$gender=$d['gender'];$dob=$d['dob']?:null;$phone=$d['phone']?:null;$address=$d['address']??null;$st->bind_param('sssss',$name,$gender,$dob,$phone,$address);$st->execute();$id=(int)$st->insert_id;$st->close();return $id;}

public function update(int $id,array $d):bool{$st=$this->db->prepare('UPDATE patients SET name=?,gender=?,dob=?,phone=?,address=? WHERE id=?');$name=$d['name'];$gender=$d['gender'];$dob=$d['dob']?:null;$phone=$d['phone']?:null;$address=$d['address']??null;$st->bind_param('sssssi',$name,$gender,$dob,$phone,$address,$id);$ok=$st->execute();$st->close();return $ok;}

public function delete(int $id,bool $soft=true):bool{if($soft){$st=$this->db->prepare('UPDATE patients SET deleted_at=NOW() WHERE id=?');$st->bind_param('i',$id);$ok=$st->execute();$st->close();return $ok;} $st=$this->db->prepare('DELETE FROM patients WHERE id=?');$st->bind_param('i',$id);$ok=$st->execute();$st->close();return $ok;}

public function countDeleted():int{$st=$this->db->prepare('SELECT COUNT(*) AS c FROM patients WHERE deleted_at IS NOT NULL');$st->execute();$res=$st->get_result();$c=(int)($res->fetch_assoc()['c']??0);$st->close();return $c;}

public function getDeleted(int $limit=10,int $offset=0):array{$rows=[];$sql="SELECT id,name,gender,dob,phone,deleted_at FROM patients WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC LIMIT ? OFFSET ?";$st=$this->db->prepare($sql);$st->bind_param('ii',$limit,$offset);$st->execute();$res=$st->get_result();while($r=$res->fetch_assoc()){$rows[]=$r;}$st->close();return $rows;}

public function restore(int $id):bool{$st=$this->db->prepare('UPDATE patients SET deleted_at=NULL WHERE id=?');$st->bind_param('i',$id);$ok=$st->execute();$st->close();return $ok;}}
