<!DOCTYPE html>
<html>
<head>
    <title>Midtrans Checkout</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>
    <h1>Midtrans Payment</h1>
    <form id="payment-form">
        <input type="text" name="name" placeholder="Nama" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="number" name="amount" placeholder="Jumlah (IDR)" required><br><br>
        <button type="submit">Bayar</button>
    </form>

    <script>
        const form = document.getElementById('payment-form');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            fetch('/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: form.name.value,
                    email: form.email.value,
                    amount: form.amount.value,
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.snap_token){
                    snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            alert('Pembayaran berhasil! Transaction ID: ' + result.transaction_id);
                        },
                        onPending: function(result){
                            alert('Pembayaran tertunda.');
                        },
                        onError: function(result){
                            alert('Pembayaran gagal.');
                        }
                    });
                } else if(data.error){
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => alert('Error: ' + error));
        });
    </script>
</body>
</html>
