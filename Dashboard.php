<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Tutorial/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    .sidebar {
      transition: width 0.3s;
    }
    .sidebar-expanded {
      width: 240px;
    }
    .sidebar-collapsed {
      width: 60px;
    }
  </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
  <!-- Sidebar -->
  <div id="sidebar" class="sidebar sidebar-expanded bg-gray-800 text-white flex flex-col">
    <div class="flex items-center justify-between p-4">
      <span class="text-lg font-bold">Admin Dashboard</span>
      <button id="toggleSidebar" class="text-white">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 5h12a1 1 0 010 2H4a1 1 0 110-2zm0 4h12a1 1 0 010 2H4a1 1 0 110-2zm0 4h12a1 1 0 010 2H4a1 1 0 110-2z" clip-rule="evenodd"/></svg>
      </button>
    </div>
    <nav class="flex flex-col space-y-2 p-4">
      <a href="#" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 12.707a1 1 0 01-1.414-1.414l2-2a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293z"/></svg>
        <span>Dashboard</span>
      </a>
      <a href="#" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM10 5a3 3 0 100 6 3 3 0 000-6zm0 12a7 7 0 01-5.467-2.57 4.978 4.978 0 0110.933 0A7 7 0 0110 17z"/></svg>
        <span>Profile</span>
      </a>
      <a href="#" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2h-1v2h1a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 012-2h1V9H4a2 2 0 01-2-2V5zm3 4h8V5H5v4zm10 4h1v2h-1v-2zm-8 0h6v2H7v-2z"/></svg>
        <span>Settings</span>
      </a>
      <a href="#" class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-md">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 12.707a1 1 0 01-1.414-1.414l2-2a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293z"/></svg>
        <span>Notifications</span>
      </a>
    </nav>
  </div>

  <!-- Main content -->
  <div class="flex-1 flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow p-4">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <div class="flex items-center space-x-4">
          <button class="text-gray-600 hover:text-gray-800">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 12.707a1 1 0 01-1.414-1.414l2-2a1 1 0 011.414 0l2 2a1 1 0 01-1.414 1.414L10 11.414l-1.293 1.293z"/></svg>
          </button>
          <button class="text-gray-600 hover:text-gray-800">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM10 7a3 3 0 110 6 3 3 0 010-6z"/></svg>
          </button>
        </div>
      </div>
    </header>

  
    <main class="flex-1 p-4 overflow-y-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
          <h2 class="text-lg font-semibold mb-2">Statistics Overview</h2>
          <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
          <h2 class="text-lg font-semibold mb-2">Recent Activity</h2>
          <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
          <h2 class="text-lg font-semibold mb-2">User Engagement</h2>
          <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
          <h2 class="text-lg font-semibold mb-2">Sales Report</h2>
          <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
          <h2 class="text-lg font-semibold mb-2">Customer Feedback</h2>
          <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow">
          <h2 class="text-lg font-semibold mb-2">Upcoming Events</h2>
          <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet.</p>
        </div>
      </div>
      
   
