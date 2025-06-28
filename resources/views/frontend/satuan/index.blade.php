@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        @include('components.flash-message')
        <div class="d-flex justify-content-between mb-3">
            <h4>Data Satuan Barang</h4>
            @can('create_satuan')
                <button type="button" class="btn btn-primary btn-labeled btn-labeled-start mb-2" data-bs-toggle="modal"
                    data-bs-target="#createSatuanModal">
                    <span class="btn-labeled-icon bg-black bg-opacity-20">
                        <i class="icon-database-add"></i>
                    </span> Tambah Satuan Barang
                </button>
            @endcan
        </div>


        <!-- Table Satuan -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Table Satuan</h5>
            </div>
            <table class="table datatable-button-html5-basic">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Satuan</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($satuans as $key => $satuan)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $satuan['name'] }}</td>
                            <td>{{ $satuan['description'] ?? '-' }}</td>
                            <td>
                                <div class="d-inline-flex">
                                    @can('view_satuan')
                                        <a href="#" class="text-info me-2" data-bs-toggle="modal"
                                            data-bs-target="#detailSatuanModal{{ $satuan['id'] }}">
                                            <i class="ph-eye"></i>
                                        </a>
                                    @endcan
                                    @can('update_satuan')
                                    <a href="#" class="text-warning me-2" data-bs-toggle="modal"
                                        data-bs-target="#editSatuanModal{{ $satuan['id'] }}">
                                        <i class="ph-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('delete_satuan')
                                        <a href="#" class="text-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteSatuanModal{{ $satuan['id'] }}">
                                            <i class="ph-trash"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('frontend.satuan.create-modal')
    @foreach ($satuans as $satuan)
        @include('frontend.satuan.detail-modal', ['satuan' => $satuan])
        @include('frontend.satuan.edit-modal', ['satuan' => $satuan])
        @include('frontend.satuan.delete-modal', ['satuan' => $satuan])
    @endforeach
@endsection
