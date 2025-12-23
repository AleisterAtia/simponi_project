@extends('kasir.kasir')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 mt-6">
        <div class="py-4">

            {{-- Header Section --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight text-gray-800">Manajemen Member</h2>
                    <p class="text-gray-500 text-sm mt-1">Kelola data pelanggan dan member point of sale.</p>
                </div>
                <button id="btnAdd"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Tambah Member
                </button>
            </div>

            {{-- Table Section --}}
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div class="inline-block min-w-full shadow-md rounded-lg overflow-hidden border border-gray-200">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Email
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Phone
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Tgl. Lahir
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($customers as $c)
                                <tr class="hover:bg-orange-50 transition duration-200">
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                {{-- Avatar Placeholder --}}
                                                <div
                                                    class="w-full h-full rounded-full bg-orange-100 flex items-center justify-center text-orange-500 font-bold">
                                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-gray-900 font-medium whitespace-no-wrap">
                                                    {{ $c->name }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $c->user->email ?? $c->email }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <span
                                            class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <span aria-hidden="true"
                                                class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                            <span class="relative text-xs">{{ $c->phone }}</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            {{ $c->birth_date ? \Carbon\Carbon::parse($c->birth_date)->format('d M Y') : '-' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                                        <div class="flex justify-center gap-2">
                                            <button
                                                class="btnEdit text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition"
                                                data-id="{{ $c->id }}" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </button>
                                            <button
                                                class="btnDelete text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"
                                                data-id="{{ $c->id }}" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- TAILWIND MODAL --}}
    <div id="customerModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="modalBackdrop">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                                Tambah Member
                            </h3>
                            <div class="mt-4">
                                <form id="customerForm">
                                    @csrf
                                    <input type="hidden" id="id">

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2"
                                            for="name">Nama</label>
                                        <input
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                            id="name" type="text" required placeholder="Nama Lengkap">
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2"
                                            for="email">Email</label>
                                        <input
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                            id="email" type="email" required placeholder="email@contoh.com">
                                    </div>

                                    <div class="mb-4 passwordField">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password
                                            <span class="text-xs font-normal text-gray-500">(Kosongkan jika
                                                edit)</span></label>
                                        <input
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                            id="password" type="password">
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">No.
                                                Telepon</label>
                                            <input
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                id="phone" type="text">
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-gray-700 text-sm font-bold mb-2"
                                                for="birth_date">Tanggal Lahir</label>
                                            <input
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                id="birth_date" type="date">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="btnSave"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" id="btnCancel"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Setup CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // --- Fungsi Modal Tailwind ---
            function openModal() {
                $('#customerModal').removeClass('hidden');
                setTimeout(() => {
                    $('#customerModal .bg-white').removeClass(
                        'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95');
                }, 50); // Delay sedikit animasi
            }

            function closeModal() {
                $('#customerModal').addClass('hidden');
            }

            $('#btnCancel, #modalBackdrop').click(function() {
                closeModal();
            });

            // --- Logic Aplikasi ---

            // TAMBAH DATA
            $('#btnAdd').click(function() {
                $('#modalTitle').text('Tambah Member Baru');
                $('#customerForm')[0].reset();
                $('.passwordField').show(); // Tampilkan field password
                $('#id').val('');
                openModal();
            });

            // SIMPAN DATA
            $('#btnSave').click(function() {
                let id = $('#id').val();
                let url = id ? '{{ route('kasir.customers.update', ':id') }}'.replace(':id', id) :
                    '{{ route('kasir.customers.store') }}';
                let method = id ? 'PUT' : 'POST';

                let dataKirim = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    birth_date: $('#birth_date').val(),
                };

                // Hanya kirim password jika form tambah atau field diisi
                if (!id || $('#password').val()) {
                    dataKirim.password = $('#password').val();
                }

                if (method === 'PUT') dataKirim._method = 'PUT';

                // Loading state button
                let btn = $(this);
                btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    type: (method === 'PUT') ? 'POST' : method,
                    data: dataKirim,
                    success: function(response) {
                        closeModal();
                        // Optional: SweetAlert atau reload
                        location.reload();
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Simpan');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let msg = '';
                            for (let key in errors) msg += errors[key][0] + '\n';
                            alert('Validasi Gagal:\n' + msg);
                        } else {
                            alert('Terjadi kesalahan sistem.');
                        }
                    }
                });
            });

            // EDIT DATA
            $(document).on('click', '.btnEdit', function() {
                let id = $(this).data('id');
                let url = '{{ route('kasir.customers.show', ':id') }}'.replace(':id', id);

                $.get(url, function(data) {
                    $('#modalTitle').text('Edit Member');
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    $('#birth_date').val(data.birth_date);

                    $('.passwordField').hide(); // Sembunyikan password saat edit (opsional)
                    $('#password').val('');

                    openModal();
                }).fail(function() {
                    alert('Gagal mengambil data.');
                });
            });

            // HAPUS DATA
            $(document).on('click', '.btnDelete', function() {
                let id = $(this).data('id');
                let url = '{{ route('kasir.customers.destroy', ':id') }}'.replace(':id', id);

                if (confirm('Yakin ingin menghapus member ini?')) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function() {
                            location.reload();
                        },
                        error: function() {
                            alert('Gagal menghapus data.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
