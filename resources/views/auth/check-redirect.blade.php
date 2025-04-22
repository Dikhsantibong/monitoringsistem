<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pendingDiscussion = sessionStorage.getItem('pendingDiscussion');
            if (pendingDiscussion) {
                const data = JSON.parse(pendingDiscussion);
                const baseUrl = window.location.href.includes('/public/') ? '/public' : '';
                const redirectUrl = `${baseUrl}${data.returnUrl}?${new URLSearchParams(data.params).toString()}`;
                
                // Hapus data dari session storage
                sessionStorage.removeItem('pendingDiscussion');
                
                // Redirect ke halaman create discussion
                window.location.href = redirectUrl;
            } else {
                // Jika tidak ada pending discussion, redirect ke dashboard
                window.location.href = '{{ $defaultRedirect }}';
            }
        });
    </script>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <p>Redirecting...</p>
    </div>
</body>
</html> 