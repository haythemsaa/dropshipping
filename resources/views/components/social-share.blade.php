@props(['url', 'title', 'description' => '', 'image' => ''])

@php
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($title);
    $encodedDescription = urlencode($description);
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span class="text-sm font-medium text-gray-700">Partager:</span>

    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       title="Partager sur Facebook"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-[#1877F2] text-white hover:bg-[#166FE5] transition shadow-md hover:shadow-lg">
        <i class="fab fa-facebook-f"></i>
    </a>

    <!-- Twitter/X -->
    <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}"
       target="_blank"
       rel="noopener noreferrer"
       title="Partager sur Twitter"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-black text-white hover:bg-gray-800 transition shadow-md hover:shadow-lg">
        <i class="fab fa-x-twitter"></i>
    </a>

    <!-- WhatsApp -->
    <a href="https://wa.me/?text={{ $encodedTitle }}%20{{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       title="Partager sur WhatsApp"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-[#25D366] text-white hover:bg-[#1EBE57] transition shadow-md hover:shadow-lg">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- LinkedIn -->
    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}"
       target="_blank"
       rel="noopener noreferrer"
       title="Partager sur LinkedIn"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-[#0A66C2] text-white hover:bg-[#004182] transition shadow-md hover:shadow-lg">
        <i class="fab fa-linkedin-in"></i>
    </a>

    <!-- Email -->
    <a href="mailto:?subject={{ $encodedTitle }}&body={{ $encodedDescription }}%20{{ $encodedUrl }}"
       title="Partager par email"
       class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-600 text-white hover:bg-gray-700 transition shadow-md hover:shadow-lg">
        <i class="fas fa-envelope"></i>
    </a>

    <!-- Copy Link -->
    <button type="button"
            onclick="copyToClipboard('{{ $url }}')"
            title="Copier le lien"
            class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 transition shadow-md hover:shadow-lg">
        <i class="fas fa-link"></i>
    </button>
</div>

<script>
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Lien copié dans le presse-papiers!');
        }).catch(err => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        document.execCommand('copy');
        alert('Lien copié dans le presse-papiers!');
    } catch (err) {
        alert('Impossible de copier le lien');
    }

    document.body.removeChild(textArea);
}
</script>
