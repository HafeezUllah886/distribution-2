@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Notifications</h4>
                    <div>
                        <button class="btn btn-sm btn-success" onclick="markAllRead()">Mark All as Read</button>
                        <button class="btn btn-sm btn-danger" onclick="clearAll()">Clear All</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-nowrap">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Title</th>
                                    <th style="width:40%; max-width:300px;">Message</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                    <tr class="{{ $notification->status == 'unread' ? 'table-warning' : '' }}">
                                        <td>
                                            @if ($notification->status == 'unread')
                                                <span class="badge bg-warning">Unread</span>
                                            @else
                                                <span class="badge bg-success">Read</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->title }}</td>
                                        <td style="max-width:300px; white-space:normal; word-break:break-word;">{{ $notification->message }}</td>
                                        <td>
                                            @switch($notification->type)
                                                @case('success')
                                                    <span class="badge bg-success" title="Success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </span>
                                                @break

                                                @case('error')
                                                    <span class="badge bg-danger" title="Error">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </span>
                                                @break

                                                @case('warning')
                                                    <span class="badge bg-warning" title="Warning">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge bg-info" title="{{ ucfirst($notification->type) }}">
                                                        <i class="bi bi-info-circle-fill"></i>
                                                    </span>
                                            @endswitch
                                        </td>
                                        <td>{{ $notification->created_at->format('d M Y, h:i A') }}</td>
                                        <td>
                                            @if ($notification->status == 'unread')
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="markAsRead({{ $notification->id }})">Mark Read</button>
                                            @endif
                                            <button class="btn btn-sm btn-danger"
                                                onclick="deleteNotification({{ $notification->id }})">Delete</button>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No notifications found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('page-js')
        <script>
            function markAsRead(id) {
                $.post('{{ route('notifications.mark-as-read', ':id') }}'.replace(':id', id), {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    }
                });
            }

            function markAllRead() {
                $.post('{{ route('notifications.mark-all-read') }}', {
                    _token: '{{ csrf_token() }}'
                }, function(response) {
                    if (response.success) {
                        location.reload();
                    }
                });
            }

            function deleteNotification(id) {
                if (confirm('Are you sure you want to delete this notification?')) {
                    $.ajax({
                        url: '{{ route('notifications.destroy', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        }
                    });
                }
            }

            function clearAll() {
                if (confirm('Are you sure you want to clear all notifications?')) {
                    $.ajax({
                        url: '{{ route('notifications.clear-all') }}',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        }
                    });
                }
            }
        </script>
    @endpush
