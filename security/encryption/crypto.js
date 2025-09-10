class CryptoManager {
    constructor() {
        this.algorithm = 'AES-GCM';
        this.keyLength = 256;
        this.ivLength = 12;
        this.tagLength = 16;
        this.saltLength = 16;
        this.iterations = 100000;
        this.keys = new Map();
    }

    async generateKey() {
        return await crypto.subtle.generateKey(
            {
                name: this.algorithm,
                length: this.keyLength
            },
            true,
            ['encrypt', 'decrypt']
        );
    }

    async deriveKeyFromPassword(password, salt) {
        const encoder = new TextEncoder();
        const passwordBuffer = encoder.encode(password);
        
        const baseKey = await crypto.subtle.importKey(
            'raw',
            passwordBuffer,
            'PBKDF2',
            false,
            ['deriveKey']
        );

        return await crypto.subtle.deriveKey(
            {
                name: 'PBKDF2',
                salt: salt,
                iterations: this.iterations,
                hash: 'SHA-256'
            },
            baseKey,
            {
                name: this.algorithm,
                length: this.keyLength
            },
            true,
            ['encrypt', 'decrypt']
        );
    }

    generateSalt() {
        return crypto.getRandomValues(new Uint8Array(this.saltLength));
    }

    generateIV() {
        return crypto.getRandomValues(new Uint8Array(this.ivLength));
    }

    async encrypt(data, key, additionalData = null) {
        const encoder = new TextEncoder();
        const dataBuffer = typeof data === 'string' ? encoder.encode(data) : data;
        const iv = this.generateIV();

        const encryptParams = {
            name: this.algorithm,
            iv: iv
        };

        if (additionalData) {
            encryptParams.additionalData = typeof additionalData === 'string' 
                ? encoder.encode(additionalData) 
                : additionalData;
        }

        const encrypted = await crypto.subtle.encrypt(
            encryptParams,
            key,
            dataBuffer
        );

        const result = new Uint8Array(iv.length + encrypted.byteLength);
        result.set(iv, 0);
        result.set(new Uint8Array(encrypted), iv.length);

        return result;
    }

    async decrypt(encryptedData, key, additionalData = null) {
        const iv = encryptedData.slice(0, this.ivLength);
        const ciphertext = encryptedData.slice(this.ivLength);

        const decryptParams = {
            name: this.algorithm,
            iv: iv
        };

        if (additionalData) {
            const encoder = new TextEncoder();
            decryptParams.additionalData = typeof additionalData === 'string' 
                ? encoder.encode(additionalData) 
                : additionalData;
        }

        const decrypted = await crypto.subtle.decrypt(
            decryptParams,
            key,
            ciphertext
        );

        return new Uint8Array(decrypted);
    }

    async encryptWithPassword(data, password, additionalData = null) {
        const salt = this.generateSalt();
        const key = await this.deriveKeyFromPassword(password, salt);
        const encrypted = await this.encrypt(data, key, additionalData);

        const result = new Uint8Array(salt.length + encrypted.length);
        result.set(salt, 0);
        result.set(encrypted, salt.length);

        return result;
    }

    async decryptWithPassword(encryptedData, password, additionalData = null) {
        const salt = encryptedData.slice(0, this.saltLength);
        const encrypted = encryptedData.slice(this.saltLength);
        
        const key = await this.deriveKeyFromPassword(password, salt);
        return await this.decrypt(encrypted, key, additionalData);
    }

    async hash(data, algorithm = 'SHA-256') {
        const encoder = new TextEncoder();
        const dataBuffer = typeof data === 'string' ? encoder.encode(data) : data;
        const hashBuffer = await crypto.subtle.digest(algorithm, dataBuffer);
        return new Uint8Array(hashBuffer);
    }

    async hmac(data, key, algorithm = 'SHA-256') {
        const encoder = new TextEncoder();
        const dataBuffer = typeof data === 'string' ? encoder.encode(data) : data;
        
        let cryptoKey;
        if (key instanceof CryptoKey) {
            cryptoKey = key;
        } else {
            const keyBuffer = typeof key === 'string' ? encoder.encode(key) : key;
            cryptoKey = await crypto.subtle.importKey(
                'raw',
                keyBuffer,
                { name: 'HMAC', hash: algorithm },
                false,
                ['sign']
            );
        }

        const signature = await crypto.subtle.sign('HMAC', cryptoKey, dataBuffer);
        return new Uint8Array(signature);
    }

    async verifyHmac(data, signature, key, algorithm = 'SHA-256') {
        const encoder = new TextEncoder();
        const dataBuffer = typeof data === 'string' ? encoder.encode(data) : data;
        
        let cryptoKey;
        if (key instanceof CryptoKey) {
            cryptoKey = key;
        } else {
            const keyBuffer = typeof key === 'string' ? encoder.encode(key) : key;
            cryptoKey = await crypto.subtle.importKey(
                'raw',
                keyBuffer,
                { name: 'HMAC', hash: algorithm },
                false,
                ['verify']
            );
        }

        return await crypto.subtle.verify('HMAC', cryptoKey, signature, dataBuffer);
    }

    arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    base64ToArrayBuffer(base64) {
        const binary = atob(base64);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes;
    }

    arrayBufferToHex(buffer) {
        const bytes = new Uint8Array(buffer);
        return Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');
    }

    hexToArrayBuffer(hex) {
        const bytes = new Uint8Array(hex.length / 2);
        for (let i = 0; i < hex.length; i += 2) {
            bytes[i / 2] = parseInt(hex.substr(i, 2), 16);
        }
        return bytes;
    }

    async exportKey(key, format = 'raw') {
        return await crypto.subtle.exportKey(format, key);
    }

    async importKey(keyData, format = 'raw', algorithm = this.algorithm, extractable = true, keyUsages = ['encrypt', 'decrypt']) {
        return await crypto.subtle.importKey(
            format,
            keyData,
            { name: algorithm, length: this.keyLength },
            extractable,
            keyUsages
        );
    }

    async generateKeyPair() {
        return await crypto.subtle.generateKey(
            {
                name: 'RSA-OAEP',
                modulusLength: 2048,
                publicExponent: new Uint8Array([1, 0, 1]),
                hash: 'SHA-256'
            },
            true,
            ['encrypt', 'decrypt']
        );
    }

    async encryptWithPublicKey(data, publicKey) {
        const encoder = new TextEncoder();
        const dataBuffer = typeof data === 'string' ? encoder.encode(data) : data;
        
        return await crypto.subtle.encrypt(
            { name: 'RSA-OAEP' },
            publicKey,
            dataBuffer
        );
    }

    async decryptWithPrivateKey(encryptedData, privateKey) {
        const decrypted = await crypto.subtle.decrypt(
            { name: 'RSA-OAEP' },
            privateKey,
            encryptedData
        );
        
        return new Uint8Array(decrypted);
    }

    async sign(data, privateKey, algorithm = 'RSA-PSS') {
        const encoder = new TextEncoder();
        const dataBuffer = typeof data === 'string' ? encoder.encode(data) : data;
        
        const signParams = algorithm === 'RSA-PSS' 
            ? { name: 'RSA-PSS', saltLength: 32 }
            : { name: 'RSASSA-PKCS1-v1_5' };
        
        return await crypto.subtle.sign(
            signParams,
            privateKey,
            dataBuffer
        );
    }

    async verify(data, signature, publicKey, algorithm = 'RSA-PSS') {
        const encoder = new TextEncoder();
        const dataBuffer = typeof data === 'string' ? encoder.encode(data) : data;
        
        const verifyParams = algorithm === 'RSA-PSS' 
            ? { name: 'RSA-PSS', saltLength: 32 }
            : { name: 'RSASSA-PKCS1-v1_5' };
        
        return await crypto.subtle.verify(
            verifyParams,
            publicKey,
            signature,
            dataBuffer
        );
    }

    generateSecureRandom(length) {
        return crypto.getRandomValues(new Uint8Array(length));
    }

    async secureCompare(a, b) {
        if (a.length !== b.length) {
            return false;
        }
        
        let result = 0;
        for (let i = 0; i < a.length; i++) {
            result |= a[i] ^ b[i];
        }
        
        return result === 0;
    }

    storeKey(keyId, key) {
        this.keys.set(keyId, key);
    }

    getKey(keyId) {
        return this.keys.get(keyId);
    }

    deleteKey(keyId) {
        return this.keys.delete(keyId);
    }

    clearKeys() {
        this.keys.clear();
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = CryptoManager;
} else {
    window.CryptoManager = CryptoManager;
}