<?php
// Ricevi i dati JSON
$data = json_decode(file_get_contents("php://input"), true);

$nome = htmlspecialchars($data['nome']);
$email = htmlspecialchars($data['email']);
$quantita = htmlspecialchars($data['quantita']);
$prodotto = htmlspecialchars($data['prodotto']);

// Email destinatario
$to = "mddmelektrisch@gmail.com";

// Oggetto e corpo dell'email
$subject = "Nuovo ordine dal sito";
$message = "Hai ricevuto un nuovo ordine:\n\n";
$message .= "Nome: $nome\n";
$message .= "Email: $email\n";
$message .= "Quantità: $quantita\n";
$message .= "Prodotto ID: $prodotto\n";

// Headers
$headers = "From: sito@tuodominio.com\r\n";
$headers .= "Reply-To: $email\r\n";

// Invia l’email
if (mail($to, $subject, $message, $headers)) {
  echo "Ordine inviato con successo!";
} else {
  echo "Errore nell'invio dell'ordine.";
}
?>
