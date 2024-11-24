<div id="copiedMessage" 
    class="fixed left-1/2 transform -translate-x-1/2 -translate-y-full bg-green-100 text-green-800 text-s px-6 py-1 rounded shadow-md hidden">
    {{ $label ? $label : "Copied!" }}
</div>

<script>
    function copyToken() {
        const token = "{{ $value }}";
        const copiedMessage = document.getElementById('copiedMessage');

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(token).then(() => {
                copiedMessage.classList.remove('hidden');
                setTimeout(() => {
                    copiedMessage.classList.add('hidden');
                }, 1000);
            }).catch(err => {
                console.error('Failed to copy token:', err);
            });
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = token;
            textArea.style.position = 'fixed'; // Avoid scrolling to the bottom
            textArea.style.left = '-9999px'; // Move off-screen
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    copiedMessage.classList.remove('hidden');
                    setTimeout(() => {
                        copiedMessage.classList.add('hidden');
                    }, 1000);
                }
            } catch (err) {
                console.error('Fallback: Unable to copy token:', err);
            }
            document.body.removeChild(textArea);
        }
    }
</script>