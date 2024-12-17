<div id="timer" class="text-lg font-bold text-gray-800 mb-4" style="display: none;">00:00:00</div>

<script>
    let timerInterval;
    let startTime;
    let elapsedTime = 0; // Menyimpan waktu yang telah berlalu
    let isRunning = true; // Set ke true untuk memulai timer

    // Cek apakah timer sedang berjalan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        const storedStartTime = localStorage.getItem('startTime');
        const storedElapsedTime = localStorage.getItem('elapsedTime');
        const storedIsRunning = localStorage.getItem('isRunning');

        if (storedStartTime && storedIsRunning === 'true') {
            startTime = new Date(parseInt(storedStartTime));
            elapsedTime = parseInt(storedElapsedTime) || 0; // Ambil waktu yang telah berlalu
            isRunning = true;
            updateTimerDisplay(); // Perbarui tampilan timer
            timerInterval = setInterval(updateTimer, 1000); // Mulai interval

            // Tampilkan timer
            document.getElementById('timer').style.display = 'block'; // Tampilkan timer
        } else {
            // Jika timer tidak berjalan, sembunyikan timer
            document.getElementById('timer').style.display = 'none';
        }
    });

    function updateTimer() {
        const now = new Date();
        elapsedTime += 1000; // Tambahkan 1 detik ke waktu yang telah berlalu
        localStorage.setItem('elapsedTime', elapsedTime); // Simpan waktu yang telah berlalu

        updateTimerDisplay(); // Perbarui tampilan timer
    }

    function updateTimerDisplay() {
        const totalElapsedTime = elapsedTime + (isRunning ? new Date() - startTime : 0);
        const hours = Math.floor(totalElapsedTime / (1000 * 60 * 60));
        const minutes = Math.floor((totalElapsedTime % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((totalElapsedTime % (1000 * 60)) / 1000);

        const timerDisplay = document.getElementById('timer');
        timerDisplay.textContent = `${padNumber(hours)}:${padNumber(minutes)}:${padNumber(seconds)}`;
    }

    function padNumber(number) {
        return number.toString().padStart(2, '0');
    }
</script> 