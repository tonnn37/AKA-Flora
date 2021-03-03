
<?php
//fetch.php
require 'connect.php';
$output = '';
$query = "";
$id = $_POST['extra_search'];

date_default_timezone_set("Asia/Bangkok");
$date1 = date("Y-m-d");

function CalDate($time1, $time2)
{

    /*    date_default_timezone_set("Asia/Bangkok");
    $date1 = new DateTime("now");       //วันปัจจุบัน;
    $date2 = new DateTime($time1); //วันที่รับเข้ามา
    $dateDiff = $date1->diff($date2);  // หาผลต่างระหว่าง 2 วัน

    $day = $dateDiff->format('%R %d'); //ผลลัพ
    
    return   $day; */

    //Convert date to formate Timpstamp
    $time1 = strtotime($time1);
    $time2 = strtotime($time2);

    //$diffdate=$time1-$time2
    $distanceInSeconds = round(abs($time2 - $time1)); //จะได้เป็นวินาที
    $distanceInMinutes = round($distanceInSeconds / 60); //แปลงจากวินาทีเป็นนาที

    $day = floor(abs($distanceInMinutes / 1440));

    return $day;
}

$query = "SELECT tb_order_detail.order_detail_id as order_detail_id,tb_order_detail.order_detail_amount as order_detail_amount,tb_order_detail.order_detail_status as order_detail_status,
tb_order_detail.order_detail_enddate as order_detail_enddate, tb_order_detail.ref_plant_id as ref_plant_id,
tb_order_detail.order_detail_per as order_detail_per,tb_order_detail.order_detail_total as order_detail_total,tb_order_detail.order_detail_planting_status as order_detail_planting_status,
tb_plant.plant_name as plant_name,tb_plant.plant_id as plant_id,
tb_order.order_status as order_status

FROM tb_order_detail
LEFT JOIN tb_order ON tb_order.order_id= tb_order_detail.ref_order_id
LEFT JOIN tb_plant ON tb_plant.plant_id = tb_order_detail.ref_plant_id
LEFT JOIN tb_typeplant ON tb_typeplant.type_plant_id = tb_plant.ref_type_plant
WHERE tb_order_detail.ref_order_id='$id'
ORDER BY tb_order_detail.order_detail_id ASC";



$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    $i = 1;
    $data = array();
    while ($row = mysqli_fetch_array($result)) {
        $date2 = $row['order_detail_enddate'];
        $date4 = strtotime($date1);
        $date3 = strtotime($date2);
        if ($date4 > $date3) {
            $day = "สิ้นสุด";
        } else {
            $day = CalDate($date1, $date2);
        }
        
        $days = date("d-m-Y",strtotime($row['order_detail_enddate']));

        $subdata = array();
        $subdata[] = $i;
        $subdata[] = $row['order_detail_id'];
        $subdata[] = $row['plant_name'];
        $subdata[] =  number_format($row['order_detail_amount']);
        $subdata[] =  number_format($row['order_detail_per']);
        $subdata[] =  number_format($row['order_detail_total']);
        $subdata[] = $days;
        $subdata[] = $day;
        $subdata[] = $row['order_detail_planting_status'];
        $subdata[] = $row['order_detail_status'];


        if ($row['order_detail_status'] == 'ปกติ') {
            $image = 'fas fa-times';
            $color = "btn btn-danger  btn-sm";
            $txt = "ยกเลิกข้อมูล";
            $href ="href";
            $disabled = "";
            $disabled2 = "";   
        } else if ($row['order_detail_status'] == 'ระงับ') {
            $image = 'fas fa-check';
            $color = "btn btn-success btn-sm";
            $txt = "ยกเลิกการระงับ";
            $href ="";
            $disabled = "";
            $disabled2 = "disabled";   
        } else if ($row['order_detail_status'] == 'รอส่งมอบ') {
            $image = 'fas fa-times';
            $color = "btn btn-danger  btn-sm";
            $txt = "ยกเลิกข้อมูล";
            $disabled = "";
            $href ="href";
            $disabled2 = "";   
        } else if ($row['order_detail_status'] == 'เสร็จสิ้น') {
            $image = 'fas fa-times';
            $color = "btn btn-danger btn-sm";
            $txt = "ยกเลิกข้อมูล";
            $href ="";
            $disabled = "disabled";
            $disabled2 = "disabled";   
        }else {
            $image = 'fas fa-times';
            $color = "btn btn-danger  btn-sm";
            $txt = "ยกเลิกข้อมูล";
            $disabled = "disabled";
            $href ="";
            $disabled2 = "disabled";   
        }

        if($row['order_detail_planting_status'] == 'ยังไม่ได้ทำการปลูก'){
            $hidden = "";
        }else{
            $hidden = "hidden";
        }

        if($row['order_status']== 'ระงับ'){
            $dis2 = "disabled";
        }else{
            $dis2 ="";
           
        }

        $subdata[] = ' <a>
        <button type="button" class="btn btn-sm btn-facebook"   id="btn_handover_now" data="' . $row['order_detail_id'] . '"data-status="' . $row['order_detail_status'] . '"  data-name="' . $row['plant_name'] . '" 
        data-toggle="tooltip"  title="เปลี่ยนสถานะรอส่งมอบ"  '.$hidden.' '.$dis2.' ' . $disabled . ' '.$disabled2.'>
        <i  class="fas fa-hand-holding-usd" style="color:white"></i></button></a>'.'
        <a '.$href.'="#edit_detail' . $row['order_detail_id'] . '" data-toggle="modal">
        <button type="button" class="btn btn-warning btn-sm" id="edit_details" data="' . $row['order_detail_id'] . '" data-plant="' . $row['plant_id'] . '" data-amount="' . $row['order_detail_amount'] . '"
        data-for="' . $row['order_detail_per'] . '" data-total="' . $row['order_detail_total'] . '" ' . $disabled . ' ' . $dis2 . ' '.$disabled2.'
        data-toggle="tooltip"  title="แก้ไขข้อมูล">
            <i class="fas fa-edit" style="color:white"></i></button>
        </a>' . '
 
        <button type="button" class="' . $color . '" '.$dis2.' id="btn_re_equ" data="' . $row['order_detail_id'] . '"data-status="' . $row['order_detail_status'] . '"  data-name="' . $row['plant_name'] . '" 
            data-toggle="tooltip"  title="' . $txt . '" ' . $disabled . ' >
            <i  class="' . $image . '" style="color:white"></i></button>';
        $rows[] = $subdata;

        $i++;
    }
    $json_data = array(

        "data" => $rows,
    );
    echo json_encode($json_data);
} else {
    $json_data = array(

        "data" => "",
    );
    echo json_encode($json_data);
}

?>
