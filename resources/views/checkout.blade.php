<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bayar dengan Midtrans</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6">Pembayaran Midtrans</h2>
        <form id="payment-form" class="space-y-4">
            <input name="name" placeholder="Nama" required class="w-full border p-2 rounded" />
            <input name="email" type="email" placeholder="Email" required class="w-full border p-2 rounded" />
            <input name="amount" type="number" placeholder="Jumlah (IDR)" required class="w-full border p-2 rounded" />
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded w-full">Bayar via Snap</button>
        </form>

        <hr class="my-4">

        <form id="coreapi-form" class="space-y-4">
            <input name="name" placeholder="Nama" required class="w-full border p-2 rounded" />
            <input name="email" type="email" placeholder="Email" required class="w-full border p-2 rounded" />
            <input name="amount" type="number" placeholder="Jumlah (IDR)" required class="w-full border p-2 rounded" />
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full">Bayar via Core API (VA)</button>
        </form>

        <p id="result" class="mt-4 text-sm text-center text-gray-600 hidden"></p>
    </div>

    <script>
        const snapForm = document.getElementById('payment-form');
        const coreForm = document.getElementById('coreapi-form');
        const result = document.getElementById('result');

        snapForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch('/process', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({
                    name: snapForm.name.value,
                    email: snapForm.email.value,
                    amount: snapForm.amount.value,
                })
            })
            .then(res => res.json())
            .then(data => {
                window.snap.pay(data.snap_token, {
                    onSuccess: function (res) { result.textContent = 'Sukses: ' + res.transaction_id; result.classList.remove('hidden'); },
                    onPending: function () { result.textContent = 'Pending...'; result.classList.remove('hidden'); },
                    onError: function () { result.textContent = 'Gagal'; result.classList.remove('hidden'); },
                    onClose: function () { result.textContent = 'Dibatalkan'; result.classList.remove('hidden'); },
                });
            });
        });

        coreForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch('/core-api/charge', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({
                    name: coreForm.name.value,
                    email: coreForm.email.value,
                    amount: coreForm.amount.value,
                })
            })
            .then(res => res.json())
            .then(data => {
                const va = data.data.va_numbers?.[0];
                if (va) {
                    result.textContent = `Transfer ke ${va.bank.toUpperCase()} VA: ${va.va_number}`;
                } else {
                    result.textContent = 'VA tidak tersedia';
                }
                result.classList.remove('hidden');
            });
        });
    </script>
</body>
</html>
