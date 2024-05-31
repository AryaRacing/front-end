// index.js
const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const port = 3000;

// Database connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'warung_madura'
});

db.connect((err) => {
    if (err) {
        console.error('Database connection failed:', err);
        return;
    }
    console.log('Connected to database');
});

// Middleware
app.use(bodyParser.json());
app.use(cors());

// Routes
app.get('/gudang', (req, res) => {
    db.query('SELECT * FROM Gudang', (err, results) => {
        if (err) throw err;
        res.json(results);
    });
});

app.post('/gudang', (req, res) => {
    const { nama_barang, stok, harga_jual } = req.body;
    db.query('INSERT INTO Gudang (nama_barang, stok, harga_jual) VALUES (?, ?, ?)', [nama_barang, stok, harga_jual], (err, result) => {
        if (err) throw err;
        res.json({ id: result.insertId, nama_barang, stok, harga_jual });
    });
});

app.delete('/gudang/:id', (req, res) => {
    const { id } = req.params;
    db.query('DELETE FROM Gudang WHERE id_barang = ?', [id], (err, result) => {
        if (err) throw err;
        res.json({ success: true });
    });
});

app.put('/gudang/:id', (req, res) => {
    const { id } = req.params;
    const { nama_barang, stok, harga_jual } = req.body;
    db.query('UPDATE Gudang SET nama_barang = ?, stok = ?, harga_jual = ? WHERE id_barang = ?', [nama_barang, stok, harga_jual, id], (err, result) => {
        if (err) throw err;
        res.json({ success: true });
    });
});

app.get('/transaksi', (req, res) => {
    db.query(`SELECT Transaksi.id_transaksi, Transaksi.tanggal_waktu, Gudang.nama_barang, barang_terjual.total_harga, barang_terjual.qty 
              FROM barang_terjual 
              JOIN Transaksi ON barang_terjual.id_transaksi = Transaksi.id_transaksi
              JOIN Gudang ON barang_terjual.id_barang = Gudang.id_barang`, (err, results) => {
        if (err) throw err;
        res.json(results);
    });
});

app.post('/transaksi', (req, res) => {
    const { id_transaksi, nama_barang, jumlah, tanggal_waktu, metode_pembayaran } = req.body;

    db.query('SELECT * FROM Gudang WHERE nama_barang = ?', [nama_barang], (err, results) => {
        if (err) throw err;

        const selectedBarang = results[0];
        if (!selectedBarang) return res.status(404).json({ error: 'Barang not found' });

        const total_harga = selectedBarang.harga_jual * jumlah;

        db.query('INSERT INTO Transaksi (id_transaksi, tanggal_waktu, total_harga, metode_pembayaran) VALUES (?, ?, ?, ?)', 
                 [id_transaksi, tanggal_waktu, total_harga, metode_pembayaran], (err, result) => {
            if (err) throw err;

            const id_barang = selectedBarang.id_barang;
            db.query('INSERT INTO barang_terjual (id_transaksi, id_barang, qty, total_harga) VALUES (?, ?, ?, ?)', 
                     [id_transaksi, id_barang, jumlah, total_harga], (err, result) => {
                if (err) throw err;
                res.json({ success: true });
            });
        });
    });
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
