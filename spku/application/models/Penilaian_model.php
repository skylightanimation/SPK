<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penilaian_model extends CI_Model
{   

	public function get_kriteria(){
		$query = $this->db->get('kriteria');
		return $query;	
    }
    
   public function get_subkriteria($kriteria_id){
        $sql =" SELECT `detail_sub`.`id_kriteria`,`detail_sub`.`id_subkriteria`,`subkriteria`.`range_nilai`
        from detail_sub
        join subkriteria 
        on `detail_sub`.`id_subkriteria`=`subkriteria`.`id_subkriteria`
        
        where id_kriteria =$kriteria_id
        ";
       
    $query = $this->db->query($sql);

if($query->num_rows() > 0) {
//$messages = array();
return $query;


}
	}

	function get_bobotsk($subkriteria_id){
        $this->db->where('id_subkriteria', $subkriteria_id);
        $this->db->order_by('bobotsk', 'ASC');
		$query = $this->db->get('subkriteria');
		return $query;	
    }


    public function getpenilaian(){

    
      
          $query = "SELECT `penilaian`.*,.`kriteria`.`kriteria`,`jurusan`.`jurusan`,`siswa`.`nama`
          FROM `penilaian` join `siswa`
      on `penilaian`.`id_siswa`=`siswa`.`id_siswa`
          join `kriteria`
          on `penilaian`.`id_kriteria`=`kriteria`.`id_kriteria`
          join `detail_jk`
          on `detail_jk`.`id_kriteria`=`kriteria`.`id_kriteria`
          join `jurusan`
          on `detail_jk`.`id_jurusan`=`jurusan`.`id_jurusan`
      group by siswa.id_siswa,kriteria.id_kriteria
      order by siswa.id_siswa,nilai DESC ";

          return $this->db->query($query)->result_array();

      
     



    }
    public function ubahPen()
    {
      $id_siswa =$this->input->post('id_siswa',true);
        $id_nilai = $this->input->post('id_nilai', true);
        $id_kriteria = $this->input->post('id_kriteria', true);
        $nilai = $this->input->post('nilai', true);
        
        $this->db->where('id_nilai', $id_nilai)->update('penilaian', ['id_kriteria' => $id_kriteria]);
        $this->db->where('id_nilai', $id_nilai)->update('penilaian', ['id_siswa' => $id_siswa]);
        $this->db->where('id_nilai', $id_nilai)->update('penilaian', ['nilai' => $nilai]);
    }
    
    public function tabel1(){
      $query = "SELECT `penilaian`.*,.`kriteria`.`kriteria`,`siswa`.`nama`
      FROM `penilaian` 
      join `siswa`
      on `penilaian`.`id_siswa`=`siswa`.`id_siswa`
      join `kriteria`
      on `penilaian`.`id_kriteria`=`kriteria`.`id_kriteria`
   order by id_siswa ASC
       ";

      return $this->db->query($query);



    }
    public function max(){
     
      $query = "SELECT `penilaian`.*,.`kriteria`.`kriteria`,`siswa`.`nama`
      FROM `penilaian`
      join `siswa`
      on `penilaian`.`id_siswa`=`siswa`.`id_siswa`
      join `kriteria`
      on `penilaian`.`id_kriteria`=`kriteria`.`id_kriteria`
      where jenis ='Benefit' 
      order by id_siswa ASC ";

      return $this->db->query($query)->result_array();



    }
    
    public function max1(){
     
      $query = "SELECT `penilaian`.*,.`kriteria`.`kriteria`,`siswa`.`nama`,
       CASE 
  WHEN jenis = 'Benefit' then MAX(nilai)
  else 0
  end as MX
      FROM `penilaian`
      join `siswa`
      on `penilaian`.`id_siswa`=`siswa`.`id_siswa`
      join `kriteria`
      on `penilaian`.`id_kriteria`=`kriteria`.`id_kriteria`
      where jenis ='Benefit'
      GROUP BY kriteria
      order by id_siswa DESC";

      return $this->db->query($query);

    }

    public function max2(){
   $query = "SELECT nilai,MX,kriteria1.kriteria,siswa.nama
   FROM penilaian INNER JOIN (SELECT kriteria,kriteria.id_kriteria,MAX(nilai) AS MX from kriteria join penilaian on
   penilaian.id_kriteria = kriteria.id_kriteria
   where jenis ='benefit' group by kriteria.id_kriteria ) as kriteria1 
   ON penilaian.id_kriteria = kriteria1.id_kriteria
   JOIN siswa
   on penilaian.id_siswa = siswa.id_siswa
   order by siswa.id_siswa ASC
 ";
   
  return $this->db->query($query);
  }


    public function min(){
      $query = "SELECT `penilaian`.*,.`kriteria`.`kriteria`,`siswa`.`username`
      FROM `penilaian` join `siswa`
      on `penilaian`.`id_siswa`=`siswa`.`id_siswa`
      join `kriteria`
      on `penilaian`.`id_kriteria`=`kriteria`.`id_kriteria`
      where jenis ='Cost' ";

      return $this->db->query($query)->result_array();



    }
    
    public function min2(){
      $query = "SELECT nilai,MN,kriteria1.kriteria,siswa.nama
      FROM penilaian INNER JOIN (SELECT kriteria,kriteria.id_kriteria,MIN(nilai) AS MN from kriteria join penilaian on
      penilaian.id_kriteria = kriteria.id_kriteria
      where jenis ='Cost' group by kriteria.id_kriteria ) as kriteria1 
      ON penilaian.id_kriteria = kriteria1.id_kriteria
      JOIN siswa
      on penilaian.id_siswa = siswa.id_siswa
      order by siswa.id_siswa ASC
    ";
      
     return $this->db->query($query);
     }
    public function min1(){
      $query = "SELECT `penilaian`.*,.`kriteria`.`kriteria`,`siswa`.`username`,MIN(nilai) as n1
      FROM `penilaian` join `siswa`
      on `penilaian`.`id_siswa`=`siswa`.`id_siswa`
      join `kriteria`
      on `penilaian`.`id_kriteria`=`kriteria`.`id_kriteria`
      where jenis ='Cost' 
      GROUP BY kriteria ";

      return $this->db->query($query)->result_array();


}

public function max3(){
     
  $query2 = "SELECT `penilaian`.*,.`kriteria`.`kriteria`,`kriteria`.`jenis`,`siswa`.`nama`,`detail_jk`.`bobot`,`jurusan`.`jurusan`,
  CASE 
  WHEN jenis = 'Cost' then (MIN(nilai)/nilai)*bobot
  WHEN jenis = 'Benefit' then (nilai/MAX(nilai))*bobot 
  else 0
  end as N3
  FROM `penilaian`
  join `siswa`
  on `penilaian`.`id_siswa`=`siswa`.`id_siswa`
  join `kriteria`
  on `penilaian`.`id_kriteria`=`kriteria`.`id_kriteria`
  join `detail_jk`
  on `detail_jk`.`id_kriteria`=`kriteria`.`id_kriteria`
  join `jurusan`
  on `detail_jk`.`id_jurusan`=`jurusan`.`id_jurusan`
 GROUP BY id_nilai,id_detail,id_siswa,kriteria
 ";
  return $this->db->query($query2);



}
public function final1(){
$query = "SELECT  total_max,total_min
FROM PENILAIAN inner join
(
  SELECT c.id_kriteria, MAX(nilai) total_max 
  FROM kriteria as c
  INNER JOIN penilaian ON c.id_kriteria = penilaian.id_kriteria
  where jenis='Benefit' 
  GROUP BY c.id_kriteria
) a
JOIN (
  SELECT k.id_kriteria, MIN(nilai) total_min 
  FROM kriteria as k
  INNER JOIN penilaian ON k.id_kriteria = penilaian.id_kriteria
  where jenis ='Cost' 
  GROUP BY k.id_kriteria
) b
ON penilaian.id_kriteria = c.id_kriteria 
INNER JOIN
 ON c.id_kriteria=k.id_kriteria
ORDER BY c.id_kriteria";

return $this->db->query($query);

}

}