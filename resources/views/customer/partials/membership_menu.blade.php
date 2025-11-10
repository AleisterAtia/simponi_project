<div class="bg-green-100 p-4 rounded-lg mb-6">
    <h3 class="font-bold text-green-800 mb-2">Program Member Eksklusif</h3>
    <p>Hi, {{ Auth::user()->name }}! Anda memiliki {{ Auth::user()->points ?? 0 }} poin. Tukarkan sekarang untuk hadiah menarik!</p>
    <a href="{{ route('member.redeem') }}" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 mt-2 inline-block">Tukar Poin</a>
</div>
