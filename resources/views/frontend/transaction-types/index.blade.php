@extends('layouts.main')

@section('content')
    <div class="container-fluid">
{{-- components/flash-message.blade.php --}}
<div class="flash-message-container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ph-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ph-x-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ph-warning me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="ph-info me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ph-x-circle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
        <div class="d-flex justify-content-between mb-3">
            <h4>Data Jenis Transaksi</h4>
            @can('create_transaction_type')
                <button class="btn btn-primary btn-labeled btn-labeled-start mb-2" data-bs-toggle="modal" data-bs-target="#createTransactionTypeModal">
                    <span class="btn-labeled-icon bg-black bg-opacity-20">
                        <i class="icon-database-add"></i></span> Tambah Jenis Transaksi
                </button>
            @endcan
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Table Jenis Transaksi</h5>
            </div>
            <table class="table datatable-button-html5-basic">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactionTypes as $index => $transactionType)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transactionType['name'] }}</td>
                            <td>{{ $transactionType['slug'] }}</td>
                            <td>
                                <div class="d-inline-flex">
                                    @can('view_transaction_type')
                                        <a href="#" class="text-info me-2" data-bs-toggle="modal"
                                            data-bs-target="#detailTransactionTypeModal{{ $transactionType['id'] }}">
                                            <i class="ph-eye"></i>
                                        </a>
                                    @endcan
                                    @can('update_transaction_type')
                                        <a href="#" class="text-warning me-2" data-bs-toggle="modal"
                                            data-bs-target="#editTransactionTypeModal{{ $transactionType['id'] }}">
                                            <i class="ph-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('delete_transaction_type')
                                        <a href="#" class="text-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteTransactionTypeModal{{ $transactionType['id'] }}">
                                            <i class="ph-trash"></i>
                                        </a>
                                    @endcan
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('frontend.transaction-types.create-modal')

    @foreach ($transactionTypes as $transactionType)
        @include('frontend.transaction-types.detail-modal', ['transactionType' => $transactionType])
        @include('frontend.transaction-types.edit-modal', ['transactionType' => $transactionType])
        @include('frontend.transaction-types.delete-modal', ['transactionType' => $transactionType])
    @endforeach
@endsection
