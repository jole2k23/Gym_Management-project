<?php
require_once 'config.php';
require_once 'fpdf/fpdf.php';

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $training_plan_id = $_POST['training_plan_id'];
    $trainer_id = 0;
    $photo_path = $_POST['photo_path'];
    $access_card_ptf = "";

    $sql = "INSERT INTO members (first_name, last_name, email, phone_number, photo_path, training_plan_id, trainer_id, access_card_pdf_path)
    VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

    $run = $conn->prepare($sql);
    $run->bind_param("sssssiis",$first_name,$last_name,$email,$phone_number,$photo_path,$training_plan_id, $trainer_id, $access_card_ptf);
    $run->execute();

    
    $member_id = $conn->insert_id;
   
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40,10, 'Access Card');
    $pdf->Ln();
    $pdf->Cell(40,10,'Member ID: ' . $member_id);
    $pdf->Ln();
    $pdf->Cell(40,10,'Name: ' .$first_name . " " . $last_name);
    $pdf->Ln();
    $pdf->Cell(40,10,'Email: ' . $member_id);
    $pdf->Ln();
    
    $filename  = 'access_cards/access_card_' . $member_id . '.pdf';
    $pdf->Output('F', $filename);

    $sql = "UPDATE members SET access_card_pdf_path = '$filename' WHERE member_id = $member_id";
    $conn->query($sql);
    $conn->close();

    $_SESSION['success_message'] = 'Clan teretana uspesno dodat';
    header('location: admin_dashboard.php');
    exit();
}


?>