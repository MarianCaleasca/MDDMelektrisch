<?php
// Imposta header per risposta JSON
header("Content-Type: application/json");

// Legge il corpo della richiesta POST (JSON)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Controlla che tutti i campi siano presenti
if (!isset($data['nome'], $data['email'], $data['quantita'], $data['prodotto'])) {
    http_response_code(400);
    echo json_encode(["message" => "Dati mancanti"]);
    exit;
}

// Sanifica i dati
$nome = htmlspecialchars($data['nome']);
$email = htmlspecialchars($data['email']);
$quantita = htmlspecialchars($data['quantita']);
$prodotto = htmlspecialchars($data['prodotto']);

// 1️⃣ SALVATAGGIO SU FILE
$log = "Ordine da: $nome <$email>\nProdotto: $prodotto\nDescrizione: $quantita\n\n";
file_put_contents("ordini.txt", $log, FILE_APPEND);

// 2️⃣ INVIO EMAIL
$to = "mddmelektrisch@gmail.com";
$subject = "📦 Nuovo ordine dal sito";
$body = "Hai ricevuto un nuovo ordine:\n\n$log";
$headers = "From: webmaster@miosito.com\r\n" .
           "Reply-To: $email\r\n" .
           "Content-Type: text/plain; charset=UTF-8";

$mail_sent = mail($to, $subject, $body, $headers);

// Risposta al frontend
if ($mail_sent) {
    echo json_encode(["message" => "Ordine ricevuto e email inviata!"]);
} else {
    echo json_encode(["message" => "Ordine salvato, ma invio email fallito."]);
}
?>
