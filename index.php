<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Madura</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Warung Madura</h1>

        <div class="section">
            <h2>Gudang</h2>
            <form id="gudang-form">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" id="nama_barang" name="nama_barang" required><br>
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" required><br>
                <label for="harga_jual">Harga Jual:</label>
                <input type="number" id="harga_jual" name="harga_jual" required><br>
                <button type="submit">Tambah</button>
            </form>
            <table id="gudang-table" class="data-table">
                <thead>
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Harga Jual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>

        <div id="input-form-container" class="section" style="display: none;">
            <h2>Input Barang Terjual</h2>
            <form id="input-form">
                <label for="id_transaksi">ID Transaksi:</label>
                <input type="text" id="id_transaksi" name="id_transaksi" required><br>
                <label for="nama_barang_terjual">Nama Barang:</label>
                <select id="nama_barang_terjual" name="nama_barang" required>
                    <!-- Options will be populated here -->
                </select><br>
                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" name="jumlah" required><br>
                <label for="tanggal_waktu">Tanggal Waktu transaksi:</label>
                <input type="datetime-local" id="tanggal_waktu" name="tanggal_waktu" required><br>
                <label for="metode_pembayaran">Metode Pembayaran:</label>
                <input type="text" id="metode_pembayaran" name="metode_pembayaran" required><br>
                <button type="submit">Tambah</button>
            </form>
        </div>

        <div class="section">
            <h2>Data Transaksi</h2>
            <table id="transaksi-table" class="data-table">
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal Waktu</th>
                        <th>Nama Barang</th>
                        <th>Total Harga</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Fetch Gudang items
            function fetchGudang() {
                $.ajax({
                    url: 'http://localhost:3000/gudang',
                    type: 'GET',
                    success: function(response) {
                        let rows = '';
                        let options = '';
                        response.forEach(item => {
                            rows += `
                                <tr>
                                    <td>${item.id_barang}</td>
                                    <td>${item.nama_barang}</td>
                                    <td>${item.stok}</td>
                                    <td>${item.harga_jual}</td>
                                    <td>
                                        <button onclick="showInputForm(${item.id_barang})">Terjual</button>
                                        <button data-id="${item.id_barang}" data-action="deleteGudang">Hapus</button>
                                    </td>
                                </tr>
                            `;
                            options += `<option value="${item.nama_barang}">${item.nama_barang}</option>`;
                        });
                        $('#gudang-table tbody').html(rows);
                        $('#nama_barang_terjual').html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            fetchGudang();

            // Show the input form for sold items
            window.showInputForm = function(id_barang) {
                document.getElementById('input-form-container').style.display = 'block';
                $('#input-form').append('<input type="hidden" name="id_barang" value="' + id_barang + '">');
            };

            // Handle Gudang form submission
            $('#gudang-form').submit(function(event) {
                event.preventDefault();

                const data = {
                    nama_barang: $('#nama_barang').val(),
                    stok: $('#stok').val(),
                    harga_jual: $('#harga_jual').val()
                };

                $.ajax({
                    url: 'http://localhost:3000/gudang',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(response) {
                        location.reload(); // Refresh the page
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Handle delete Gudang action
            $(document).on('click', 'button[data-action="deleteGudang"]', function(event) {
                event.preventDefault();

                const id_barang = $(this).data('id');
                if (confirm('Are you sure?')) {
                    $.ajax({
                        url: 'http://localhost:3000/gudang/' + id_barang,
                        type: 'DELETE',
                        success: function(response) {
                            location.reload(); // Refresh the page
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }
            });

            // Handle Terjual form submission
            $('#input-form').submit(function(event) {
                event.preventDefault();

                const data = {
                    id_transaksi: $('#id_transaksi').val(),
                    nama_barang: $('#nama_barang_terjual').val(),
                    jumlah: $('#jumlah').val(),
                    tanggal_waktu: $('#tanggal_waktu').val(),
                    metode_pembayaran: $('#metode_pembayaran').val()
                };

                $.ajax({
                    url: 'http://localhost:3000/transaksi',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(response) {
                        location.reload(); // Refresh the page
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Fetch Transaksi data
            function fetchTransaksi() {
                $.ajax({
                    url: 'http://localhost:3000/transaksi',
                    type: 'GET',
                    success: function(response) {
                        let rows = '';
                        response.forEach(transaksi => {
                            rows += `
                                <tr>
                                    <td>${transaksi.id_transaksi}</td>
                                    <td>${transaksi.tanggal_waktu}</td>
                                    <td>${transaksi.nama_barang}</td>
                                    <td>${transaksi.total_harga}</td>
                                    <td>${transaksi.qty}</td>
                                </tr>
                            `;
                        });
                        $('#transaksi-table tbody').html(rows);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            fetchTransaksi();
        });
    </script>
</body>
</html>
