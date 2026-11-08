<div
    x-data="{
        stream: null,
        isInitializing: true,
        hasCaptured: false,
        errorMessage: null,
        capturedData: null,
        devices: [],
        selectedDeviceId: null,

        async init() {
            await this.detectDevices();
            await this.startStream();
        },

        async detectDevices() {
            try {
                if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                    const allDevices = await navigator.mediaDevices.enumerateDevices();
                    this.devices = allDevices.filter(d => d.kind === 'videoinput');
                }
            } catch (e) {
                // Ignore enumeration errors
            }
        },

        async startStream() {
            this.stopStream();
            this.isInitializing = true;
            this.errorMessage = null;

            try {
                const constraints = {
                    video: this.selectedDeviceId
                        ? { deviceId: { exact: this.selectedDeviceId } }
                        : {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1920 },
                            height: { ideal: 1080 }
                        },
                    audio: false
                };

                this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                if (this.$refs.video) {
                    this.$refs.video.srcObject = this.stream;
                    await this.$refs.video.play();
                }

                this.isInitializing = false;
            } catch (err) {
                try {
                    // Fallback ke kamera default apa pun
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    if (this.$refs.video) {
                        this.$refs.video.srcObject = this.stream;
                        await this.$refs.video.play();
                    }
                    this.isInitializing = false;
                } catch (fallbackErr) {
                    this.isInitializing = false;
                    this.errorMessage = 'Akses kamera gagal: ' + (fallbackErr.message || 'Izin kamera tidak diberikan atau perangkat kamera tidak terdeteksi.');
                }
            }
        },

        async switchCamera() {
            if (this.devices.length > 1) {
                const currentIndex = this.devices.findIndex(d => d.deviceId === this.selectedDeviceId);
                const nextIndex = (currentIndex + 1) % this.devices.length;
                this.selectedDeviceId = this.devices[nextIndex].deviceId;
                await this.startStream();
            }
        },

        takeSnapshot() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;

            if (!video || !canvas) return;

            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.88);
            this.capturedData = dataUrl;
            this.hasCaptured = true;

            $wire.set('{{ $getStatePath() }}', dataUrl);

            this.stopStream();
        },

        retake() {
            this.hasCaptured = false;
            this.capturedData = null;
            $wire.set('{{ $getStatePath() }}', null);
            this.startStream();
        },

        stopStream() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
        },

        destroy() {
            this.stopStream();
        }
    }"
    x-init="init()"
    x-on:destroy="destroy()"
    class="space-y-4"
>
    <div class="relative w-full aspect-video bg-neutral-900 rounded-xl overflow-hidden border border-neutral-700 flex items-center justify-center shadow-inner">
        <video x-ref="video" autoplay playsinline class="w-full h-full object-cover" x-show="!hasCaptured"></video>
        
        <img x-ref="preview" :src="capturedData" class="w-full h-full object-cover" x-show="hasCaptured" style="display: none;" alt="Pratinjau Foto Kamera" />

        <div x-show="errorMessage" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center bg-neutral-950/95 text-white" style="display: none;">
            <svg class="w-12 h-12 text-rose-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm font-semibold text-rose-300" x-text="errorMessage"></p>
            <p class="text-xs text-neutral-400 mt-2">Pastikan izin kamera diaktifkan pada peramban (browser) dan perangkat tidak sedang digunakan oleh aplikasi lain.</p>
        </div>

        <div x-show="isInitializing && !errorMessage" class="absolute inset-0 flex flex-col items-center justify-center bg-neutral-950 text-white">
            <svg class="animate-spin h-8 w-8 text-primary-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-sm text-neutral-300 font-medium">Menghubungkan ke kamera perangkat...</span>
        </div>
    </div>

    <canvas x-ref="canvas" class="hidden" style="display: none;"></canvas>

    <div class="flex items-center gap-3">
        <button
            type="button"
            x-show="!hasCaptured && !errorMessage && !isInitializing"
            @click="takeSnapshot()"
            class="fi-btn fi-btn-color-primary fi-btn-size-md inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-xs transition duration-75 focus-visible:ring-2 bg-emerald-600 text-white hover:bg-emerald-500 w-full cursor-pointer"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Jepret / Ambil Foto</span>
        </button>

        <button
            type="button"
            x-show="devices.length > 1 && !hasCaptured && !errorMessage && !isInitializing"
            @click="switchCamera()"
            class="fi-btn fi-btn-color-gray fi-btn-size-md inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-xs transition duration-75 focus-visible:ring-2 bg-neutral-100 text-neutral-700 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-200 cursor-pointer"
            title="Ganti Kamera"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Ganti Kamera</span>
        </button>

        <button
            type="button"
            x-show="hasCaptured"
            @click="retake()"
            class="fi-btn fi-btn-color-gray fi-btn-size-md inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold shadow-xs transition duration-75 focus-visible:ring-2 bg-neutral-200 text-neutral-800 hover:bg-neutral-300 dark:bg-neutral-800 dark:text-neutral-200 w-full cursor-pointer"
            style="display: none;"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Ambil Ulang Foto</span>
        </button>
    </div>
</div>

