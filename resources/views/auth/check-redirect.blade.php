<!DOCTYPE html>
<html>
<head>
    <title>Redirecting...</title>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for redirectAfterLogin in sessionStorage
            const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
            if (redirectUrl) {
                // Clear the stored URL
                sessionStorage.removeItem('redirectAfterLogin');
                // Redirect to the stored URL
                window.location.href = redirectUrl;
            } else {
                // If no redirect URL, go to default route
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