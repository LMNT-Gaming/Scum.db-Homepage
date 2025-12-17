require('dotenv').config();
const fs = require('fs');
const path = require('path');
const ftp = require('basic-ftp');
const SftpClient = require('ssh2-sftp-client');

const LOCAL_DB_PATH = path.resolve(__dirname, 'SCUM.db');

async function downloadFromFTP() {
    const client = new ftp.Client();
    client.ftp.verbose = false;

    try {
        console.log('🔌 Verbinde zu SCUM FTP...');
        await client.access({
            host: process.env.FTP_HOST,
            port: process.env.FTP_PORT || 21,
            user: process.env.FTP_USER,
            password: process.env.FTP_PASS,
            secure: false
        });

        console.log('⬇️ Lade SCUM.db herunter...');
        await client.downloadTo(LOCAL_DB_PATH, process.env.FTP_PATH);
        console.log('✅ SCUM.db erfolgreich von FTP geladen');
    } catch (err) {
        console.error('❌ Fehler beim FTP-Download:', err.message);
    } finally {
        client.close();
    }
	if (fs.existsSync(LOCAL_DB_PATH)) {
    console.log('📁 Lokale Datei gefunden unter:', LOCAL_DB_PATH);
    console.log('📦 Größe:', fs.statSync(LOCAL_DB_PATH).size, 'Bytes');
} else {
    console.log('⚠️ Datei wurde nicht heruntergeladen!');
}
}

async function uploadToSFTP() {
    const sftp = new SftpClient();

    try {
        console.log('🔌 Verbinde zu Strato SFTP...');
        await sftp.connect({
            host: process.env.SFTP_HOST,
            port: process.env.SFTP_PORT || 22,
            username: process.env.SFTP_USER,
            password: process.env.SFTP_PASS
        });

        console.log('⬆️ Lade SCUM.db zu Strato hoch...');
        await sftp.put(LOCAL_DB_PATH, process.env.SFTP_REMOTE_PATH);
        console.log('✅ SCUM.db erfolgreich zu Strato hochgeladen');
    } catch (err) {
        console.error('❌ Fehler beim SFTP-Upload:', err.message);
    } finally {
        sftp.end();
    }
}

(async () => {
    await downloadFromFTP();
    await uploadToSFTP();
})();
