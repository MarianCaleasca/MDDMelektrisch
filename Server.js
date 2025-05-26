// JavaScript source code
require('dotenv').config();
const mongoose = require('mongoose');

const MONGO_URI = process.env.MONGO_URI;

mongoose.connect(MONGO_URI)
    .then(() => console.log("Connesso a MongoDB"))
    .catch(err => console.error("Errore connessione MongoDB:", err));
// JavaScript source code
const express = require("express");
const mongoose = require("mongoose");
const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken");
const cors = require("cors");

const app = express();
app.use(express.json());
app.use(cors());

// 🔸 Connessione a MongoDB
mongoose.connect(MONGO_URI);

// 🔸 Schema del modello Utente
const UserSchema = new mongoose.Schema({
    username: String,
    email: String,
    password: String
});

const User = mongoose.model("User", UserSchema);

// 🔹 Registrazione
app.post("/register", async (req, res) => {
    const { username, email, password } = req.body;
    const userExists = await User.findOne({ email });

    if (userExists) return res.status(400).json({ message: "Email già registrata" });

    const hashedPassword = await bcrypt.hash(password, 10);
    const user = new User({ username, email, password: hashedPassword });
    await user.save();
    res.json({ message: "Registrazione completata!" });
});

// 🔹 Login
app.post("/login", async (req, res) => {
    const { email, password } = req.body;
    const user = await User.findOne({ email });

    if (!user) return res.status(400).json({ message: "Utente non trovato" });

    const isMatch = await bcrypt.compare(password, user.password);
    if (!isMatch) return res.status(400).json({ message: "Password errata" });

    const token = jwt.sign({ id: user._id }, process.env.JWT_SECRET, { expiresIn: "1h" });
    res.json({ token });
});

// 🔹 Dashboard (Dati utente)
app.get("/me", async (req, res) => {
    const auth = req.headers.authorization;
    if (!auth) return res.status(401).json({ message: "Token mancante" });

    try {
        const token = auth.split(" ")[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const user = await User.findById(decoded.id);
        res.json({ username: user.username, email: user.email });
    } catch (err) {
        res.status(401).json({ message: "Token non valido" });
    }
});

// 🔹 Avvio del server
const PORT = process.env.PORT || 3000;

app.use(express.static("Prova2"));  // Serve file statici dalla cartella Prova2

app.listen(PORT, '0.0.0.0', () => {
    console.log(`✅ Server avviato su http://mddmelektrisch.onrender.com:${PORT}`);
});


