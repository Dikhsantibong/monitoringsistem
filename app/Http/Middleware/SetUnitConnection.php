namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetUnitConnection
{
    public function handle(Request $request, Closure $next)
    {
        // Set default connection ke UP Kendari
        $defaultConnection = 'mysql';
        
        // Ambil unit dari request atau session
        $unit = $request->session()->get('unit', $defaultConnection);
        
        // Set connection untuk request ini
        config(['database.default' => $unit]);
        
        return $next($request);
    }
} 