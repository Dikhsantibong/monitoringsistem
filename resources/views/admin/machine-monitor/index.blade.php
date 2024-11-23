@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">MONITOR MESIN </h2>
        </div>
        <nav class="mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}" style="text-decoration: none;">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.machine-monitor') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.machine-monitor') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}" style="text-decoration: none;">
                <i class="fas fa-cogs mr-3"></i>
                <span>Machine Monitor</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}" style="text-decoration: none;">
                <i class="fas fa-users mr-3"></i>
                <span>User Management</span>
            </a>
            <a href="{{ route('admin.meetings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.meetings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}" style="text-decoration: none;">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Meeting Reports</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-blue-50' }}" style="text-decoration: none;">
                <i class="fas fa-cog mr-3"></i>
                <span>Settings</span>
            </a>
        </nav>
        
    </aside>


    <!-- Main Content Area - Fixed position -->
    <div class="fixed top-0 left-64 right-0 h-full overflow-hidden">
        <!-- Header -->
        <header class="absolute top-0 left-0 right-0 bg-white shadow-sm z-20">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Machine Monitoring Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <button onclick="openNewMachineModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-plus mr-2"></i>Add Machine
                    </button>
                    <button onclick="openNewIssueModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>Report Issue
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="h-full pt-16 overflow-y-auto scrollbar-hide">
            <div class="p-6">
                <!-- Performance Indicators -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Corrective Actions</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-1">
                                    {{ $machines->sum(function($machine) {
                                        return $machine->issues->where('status', 'closed')->count();
                                    }) }}
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-wrench text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Work Accuracy</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-1">
                                    {{ number_format($machines->flatMap->metrics->avg('achievement_percentage'), 1) }}%
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Active Issues</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-1">
                                    {{ $machines->sum(function($machine) {
                                        return $machine->issues->where('status', 'open')->count();
                                    }) }}
                                </p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Machine Health Map -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Machine Health Categories</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($healthCategories as $category)
                            <div class="border rounded-lg p-4 {{ $category->open_issues > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                                <h3 class="font-medium text-gray-800">{{ ucfirst($category->name) }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Active Issues: {{ $category->open_issues }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Issues Chart and Machine Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Monthly Issues Chart -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Monthly Issues</h2>
                            <canvas id="monthlyIssuesChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Machine Status -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-800">Machine Status</h2>
                                <div class="flex space-x-2">
                                    <button onclick="refreshMachineStatus()" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-4">
                                @foreach($machines as $machine)
                                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-800">{{ $machine->name }}</h3>
                                        <p class="text-sm text-gray-500">Code: {{ $machine->code }}</p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <!-- Status Badge -->
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            {{ $machine->status === 'START' ? 'bg-green-100 text-green-800' : 
                                               ($machine->status === 'STOP' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $machine->status }}
                                        </span>
                                        
                                        <!-- Action Buttons -->
                                        <div class="flex space-x-2">
                                            <button onclick="updateMachineStatus({{ $machine->id }}, 'START')" 
                                                    class="px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600">
                                                Start
                                            </button>
                                            <button onclick="updateMachineStatus({{ $machine->id }}, 'STOP')" 
                                                    class="px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                                                Stop
                                            </button>
                                            <button onclick="editMachine({{ $machine->id }})" 
                                                    class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                                Edit
                                            </button>
                                            <button onclick="deleteMachine({{ $machine->id }})" 
                                                    class="px-2 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Issues Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Issues</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Machine</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($recentIssues as $issue)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $issue->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $issue->machine->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $issue->category->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $issue->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $issue->status === 'open' ? 'bg-red-100 text-red-800' : 
                                                   ($issue->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($issue->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Monitoring and Statistics -->
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Monitoring dan Statistik</h2>

                <!-- Monitoring Status Mesin -->
                <div class="bg-white rounded-lg shadow mb-6 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Monitoring Status Mesin</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Machine</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Error</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($machines as $machine)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $machine->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $machine->status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $machine->status === 'START' ? 'Aktif' : 'Mati' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $machine->issues->where('status', 'open')->count() }} Error</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Statistik Uptime/Downtime -->
                <div class="bg-white rounded-lg shadow mb-6 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Uptime/Downtime</h3>
                    <canvas id="uptimeChart" height="200"></canvas>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- New Issue Modal -->
<div id="newIssueModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Report New Issue</h3>
            <form id="newIssueForm" action="{{ route('admin.machine-monitor.store-issue') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Machine</label>
                    <select name="machine_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                        @foreach($machines as $machine)
                        <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                        @foreach($healthCategories as $category)
                        <option value="{{ $category->id }}">{{ ucfirst($category->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeNewIssueModal()" class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add New Machine Modal -->
<div id="newMachineModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Machine</h3>
            <form id="newMachineForm" action="{{ route('admin.machine-monitor.store-machine') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Machine Name</label>
                    <input type="text" name="name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Machine Code</label>
                    <input type="text" name="code" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Initial Status</label>
                    <select name="status" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>
                        <option value="STOP">Stop</option>
                        <option value="START">Start</option>
                        <option value="PARALLEL">Parallel</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeNewMachineModal()" class="mr-2 px-4 py-2 text-gray-500 hover:text-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Machine Modal -->
<div id="editMachineModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <!-- Similar structure to Add Machine Modal but with pre-filled values -->
</div>

@push('scripts')
<script>
// Chart initialization
const ctx = document.getElementById('monthlyIssuesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Issues',
            data: @json(array_values($monthlyIssues)),
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Modal functions
function openNewIssueModal() {
    document.getElementById('newIssueModal').classList.remove('hidden');
}

function closeNewIssueModal() {
    document.getElementById('newIssueModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('newIssueModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNewIssueModal();
    }
});

// DataTable initialization
$(document).ready(function() {
    $('table').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']]
    });
});

// Machine CRUD Functions
function openNewMachineModal() {
    document.getElementById('newMachineModal').classList.remove('hidden');
}

function closeNewMachineModal() {
    document.getElementById('newMachineModal').classList.add('hidden');
}

function editMachine(machineId) {
    // Fetch machine details and open edit modal
    fetch(`/admin/machine-monitor/machines/${machineId}`)
        .then(response => response.json())
        .then(data => {
            // Populate edit form with machine data
            document.getElementById('editMachineModal').classList.remove('hidden');
        });
}

function deleteMachine(machineId) {
    if (confirm('Are you sure you want to delete this machine?')) {
        fetch(`/admin/machine-monitor/machines/${machineId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function updateMachineStatus(machineId, status) {
    fetch(`/admin/machine-monitor/machines/${machineId}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function refreshMachineStatus() {
    location.reload();
}

// Chart untuk Uptime/Downtime
const ctx = document.getElementById('uptimeChart').getContext('2d');
const uptimeData = @json($uptime);
const labels = uptimeData.map(machine => machine.name);
const uptimeValues = uptimeData.map(machine => machine.uptime);
const downtimeValues = uptimeData.map(machine => machine.downtime);

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Uptime',
                data: uptimeValues,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
            },
            {
                label: 'Downtime',
                data: downtimeValues,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush

<style>
/* Sembunyikan scrollbar tapi tetap bisa scroll */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Pastikan scrollbar selalu ada untuk mencegah layout shift */
html {
    overflow-y: scroll;
}

/* Mencegah bounce effect pada macOS */
body {
    overflow: hidden;
}
</style>
