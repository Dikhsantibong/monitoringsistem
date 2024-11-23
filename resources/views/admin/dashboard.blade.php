@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-4">
            <h2 class="text-xl font-bold text-blue-600">Daily Meeting App</h2>
        </div>
        <nav class="mt-4">
            <a href="#" class="flex items-center px-4 py-3 bg-blue-50 text-blue-700">
                <i class="fas fa-home mr-3"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50">
                <i class="fas fa-users mr-3"></i>
                <span>User Management</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50">
                <i class="fas fa-chart-bar mr-3"></i>
                <span>Meeting Reports</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:bg-blue-50">
                <i class="fas fa-cog mr-3"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center px-6 py-4">
                <h1 class="text-2xl font-semibold text-gray-800">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <button class="p-2 hover:bg-gray-100 rounded-full">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="p-2 hover:bg-gray-100 rounded-full">
                        Admin Name <i class="fas fa-user"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Statistics Cards -->
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card">
                        <h5><i class="fas fa-users text-primary"></i> Total Users</h5>
                        <h3>1,234</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h5><i class="fas fa-calendar text-warning"></i> Scheduled Meetings</h5>
                        <h3>56</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h5><i class="fas fa-server text-success"></i> System Uptime</h5>
                        <h3>99.9%</h3>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5>User Activity</h5>
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <h5>User Growth</h5>
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="activity-table mt-4">
                <h5>Recent Activities</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>User</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Meeting Completed</td>
                            <td>John Doe</td>
                            <td>2 hours ago</td>
                        </tr>
                        <tr>
                            <td>New User Registration</td>
                            <td>Jane Smith</td>
                            <td>3 hours ago</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p class="mb-0">&copy; 2024 Daily Meeting App v1.0</p>
        </footer>
    </div>
</div>
@endsection
