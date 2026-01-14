/* public/js/scan-kamera.js */
let scannerSiswa, scannerUnit, scannerKembali;
let isProcessing = false;
const qrConfig = {
    fps: 25,
    qrbox: { width: 180, height: 180 },
    aspectRatio: 1.0,
};

function startScannerSiswa() {
    isProcessing = false;
    if (scannerSiswa) scannerSiswa.clear();
    scannerSiswa = new Html5Qrcode("reader-siswa");
    scannerSiswa
        .start({ facingMode: "environment" }, qrConfig, (qr) => {
            if (isProcessing) return;
            processSiswaScan(qr);
        })
        .catch((err) => console.error(err));
}

function processSiswaScan(qr) {
    isProcessing = true;
    fetch(`/get-siswa-by-qr/${qr}`)
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                scannerSiswa.stop().then(() => {
                    if (data.mode === "kembali") {
                        document.getElementById("kembali-nama").innerText =
                            data.siswa.nama_siswa;
                        document.getElementById("kembali-unit").innerText =
                            "UNIT: " + data.siswa.no_unit;
                        document.getElementById(
                            "form-kembali"
                        ).action = `/peminjaman/kembali/${data.pinjaman_id}`;
                        goToStep("kembali");
                    } else {
                        document.getElementById("display-nama").innerText =
                            data.siswa.nama_siswa;
                        document.getElementById("display-kelas").innerText =
                            "Kelas: " + data.siswa.nama_kelas;
                        document.getElementById("val-siswa").value =
                            data.siswa.id;
                        goToStep(2);
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: data.message,
                    willClose: () => {
                        isProcessing = false;
                    },
                });
            }
        });
}

function startScannerUnit() {
    isProcessing = false;
    document.getElementById("val-guru").value =
        document.getElementById("guru_id_select").value;
    document.getElementById("val-mapel").value =
        document.getElementById("mapel_id_select").value;
    if (scannerUnit) scannerUnit.clear();
    scannerUnit = new Html5Qrcode("reader-unit");
    scannerUnit.start({ facingMode: "environment" }, qrConfig, (qr) => {
        if (isProcessing) return;
        isProcessing = true;
        document.getElementById("val-qr").value = qr;
        scannerUnit.stop().then(() => {
            Swal.fire({
                title: "Memproses...",
                html: "Menyimpan data",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });
            document.getElementById("finalForm").submit();
        });
    });
}

function startScannerKembali() {
    isProcessing = false;
    if (scannerKembali) scannerKembali.clear();
    scannerKembali = new Html5Qrcode("reader-kembali");
    scannerKembali.start({ facingMode: "environment" }, qrConfig, (qr) => {
        if (isProcessing) return;
        isProcessing = true;
        document.getElementById("val-qr-kembali").value = qr;
        scannerKembali.stop().then(() => {
            Swal.fire({
                title: "Memproses...",
                didOpen: () => Swal.showLoading(),
                allowOutsideClick: false,
            });
            document.getElementById("form-kembali").submit();
        });
    });
}

function goToStep(step) {
    const flipContent = document.getElementById("flip-content");
    flipContent.style.transition =
        "transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)";
    flipContent.classList.add("is-flipping");
    setTimeout(() => {
        document
            .querySelectorAll(".step-content")
            .forEach((s) => s.classList.remove("active"));
        const stepper = document.getElementById("main-stepper");
        if (step === "kembali") {
            stepper.style.display = "none";
            document.getElementById("step-kembali").classList.add("active");
            startScannerKembali();
        } else {
            stepper.style.display = "flex";
            document.getElementById(`step-${step}`).classList.add("active");
            const fill = document.getElementById("p-fill");
            fill.style.width = step === 1 ? "0%" : step === 2 ? "50%" : "100%";
            [1, 2, 3].forEach((d) => {
                const dot = document.getElementById(`dot-${d}`);
                if (d < step) {
                    dot.className = "step-dot done";
                    dot.innerHTML = '<i class="fas fa-check"></i>';
                } else if (d === step) {
                    dot.className = "step-dot active";
                    dot.innerHTML = d;
                } else {
                    dot.className = "step-dot";
                    dot.innerHTML = d;
                }
            });
            if (step === 3) startScannerUnit();
        }
    }, 300);
    setTimeout(() => {
        flipContent.style.transition = "none";
        flipContent.classList.remove("is-flipping");
    }, 600);
}

// Tambahan Event Listeners
document.addEventListener("DOMContentLoaded", () => {
    const guruSelect = document.getElementById("guru_id_select");
    if (guruSelect) {
        guruSelect.addEventListener("change", function () {
            const mSelect = document.getElementById("mapel_id_select");
            mSelect.disabled = true;
            fetch(`/get-mapel-by-guru/${this.value}`)
                .then((res) => res.json())
                .then((data) => {
                    let opt =
                        '<option value="" disabled selected>-- Pilih Mapel --</option>';
                    data.forEach(
                        (m) =>
                            (opt += `<option value="${m.id}">${m.nama_mapel}</option>`)
                    );
                    mSelect.innerHTML = opt;
                    mSelect.disabled = false;
                });
        });
    }

    const mapelSelect = document.getElementById("mapel_id_select");
    if (mapelSelect) {
        mapelSelect.addEventListener("change", () => {
            setTimeout(() => goToStep(3), 400);
        });
    }
});
