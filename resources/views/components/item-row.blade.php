@props(['barang', 'loop'])

<tr id="item-{{ $barang['kode'] }}" data-barang-kode="{{ $barang['kode'] }}">
    <td class="text-center">{{ $loop->iteration }}</td>
    <td class="text-center">{{ $barang['kode'] }}</td>
    <td class="text-center">{{ $barang['nama'] }}</td>
    <td class="text-center">
        <a href="{{ asset($barang['gambar']) }}" data-lightbox="gambar-{{ $barang['kode'] }}">
            <img src="{{ asset($barang['gambar']) }}" alt="{{ $barang['gambar'] }}"
                style="width: 60px; height: 60px; object-fit: cover;">
        </a>
    </td>
    <td class="text-center">{{ $barang['kategoribarang'] }}</td>
    <td class="text-center">{{ $barang['stok_tersedia'] }}</td>
        <td class="text-center">{{ $barang['stok_dipinjam'] }}</td>
    <td class="text-center">{{ $barang['stok_maintenance'] }}</td>

    <td class="text-center">
        <input type="number" name="quantities[{{ $barang['kode'] }}]" id="jumlah-{{ $barang['kode'] }}"
            class="form-control quantity-input" value="{{ $barang['jumlah'] }}" min="1"
            oninput="validateQuantityInput(this)" style="width: 80px;">
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem('{{ $barang['kode'] }}')">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>
