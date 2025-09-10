class OAuth2Provider {
    constructor() {
        this.providers = {
            google: {
                clientId: '',
                clientSecret: '',
                redirectUri: '',
                authUrl: 'https://accounts.google.com/o/oauth2/v2/auth',
                tokenUrl: 'https://oauth2.googleapis.com/token',
                userInfoUrl: 'https://www.googleapis.com/oauth2/v2/userinfo',
                scope: 'openid email profile'
            },
            github: {
                clientId: '',
                clientSecret: '',
                redirectUri: '',
                authUrl: 'https://github.com/login/oauth/authorize',
                tokenUrl: 'https://github.com/login/oauth/access_token',
                userInfoUrl: 'https://api.github.com/user',
                scope: 'user:email'
            },
            microsoft: {
                clientId: '',
                clientSecret: '',
                redirectUri: '',
                authUrl: 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                tokenUrl: 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                userInfoUrl: 'https://graph.microsoft.com/v1.0/me',
                scope: 'openid email profile'
            }
        };
        this.states = new Map();
        this.nonces = new Map();
    }

    configure(provider, config) {
        if (!this.providers[provider]) {
            throw new Error(`Unsupported OAuth2 provider: ${provider}`);
        }
        
        this.providers[provider] = {
            ...this.providers[provider],
            ...config
        };
    }

    generateState() {
        const array = new Uint8Array(32);
        crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    }

    generateNonce() {
        const array = new Uint8Array(16);
        crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    }

    getAuthorizationUrl(provider, options = {}) {
        const providerConfig = this.providers[provider];
        if (!providerConfig) {
            throw new Error(`Unsupported OAuth2 provider: ${provider}`);
        }

        const state = this.generateState();
        const nonce = this.generateNonce();
        
        this.states.set(state, {
            provider,
            createdAt: Date.now(),
            expiresAt: Date.now() + 600000, // 10 minutes
            ...options
        });
        
        this.nonces.set(nonce, {
            state,
            createdAt: Date.now()
        });

        const params = new URLSearchParams({
            client_id: providerConfig.clientId,
            redirect_uri: providerConfig.redirectUri,
            response_type: 'code',
            scope: providerConfig.scope,
            state: state,
            nonce: nonce
        });

        if (provider === 'microsoft') {
            params.append('response_mode', 'query');
        }

        return `${providerConfig.authUrl}?${params.toString()}`;
    }

    async handleCallback(provider, code, state, receivedNonce = null) {
        const stateData = this.states.get(state);
        if (!stateData || stateData.expiresAt < Date.now()) {
            throw new Error('Invalid or expired state parameter');
        }

        if (stateData.provider !== provider) {
            throw new Error('Provider mismatch');
        }

        this.states.delete(state);

        if (receivedNonce) {
            const nonceData = this.nonces.get(receivedNonce);
            if (!nonceData || nonceData.state !== state) {
                throw new Error('Invalid nonce parameter');
            }
            this.nonces.delete(receivedNonce);
        }

        const providerConfig = this.providers[provider];
        const tokenResponse = await this.exchangeCodeForToken(providerConfig, code);
        const userInfo = await this.getUserInfo(providerConfig, tokenResponse.access_token);

        return {
            provider,
            accessToken: tokenResponse.access_token,
            refreshToken: tokenResponse.refresh_token,
            expiresIn: tokenResponse.expires_in,
            user: this.normalizeUserInfo(provider, userInfo)
        };
    }

    async exchangeCodeForToken(providerConfig, code) {
        const params = new URLSearchParams({
            client_id: providerConfig.clientId,
            client_secret: providerConfig.clientSecret,
            code: code,
            grant_type: 'authorization_code',
            redirect_uri: providerConfig.redirectUri
        });

        const response = await fetch(providerConfig.tokenUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: params.toString()
        });

        if (!response.ok) {
            const error = await response.text();
            throw new Error(`Token exchange failed: ${error}`);
        }

        return await response.json();
    }

    async getUserInfo(providerConfig, accessToken) {
        const response = await fetch(providerConfig.userInfoUrl, {
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            const error = await response.text();
            throw new Error(`User info request failed: ${error}`);
        }

        return await response.json();
    }

    normalizeUserInfo(provider, userInfo) {
        switch (provider) {
            case 'google':
                return {
                    id: userInfo.id,
                    email: userInfo.email,
                    name: userInfo.name,
                    firstName: userInfo.given_name,
                    lastName: userInfo.family_name,
                    picture: userInfo.picture,
                    verified: userInfo.verified_email
                };
            
            case 'github':
                return {
                    id: userInfo.id.toString(),
                    email: userInfo.email,
                    name: userInfo.name || userInfo.login,
                    username: userInfo.login,
                    picture: userInfo.avatar_url,
                    verified: true
                };
            
            case 'microsoft':
                return {
                    id: userInfo.id,
                    email: userInfo.mail || userInfo.userPrincipalName,
                    name: userInfo.displayName,
                    firstName: userInfo.givenName,
                    lastName: userInfo.surname,
                    verified: true
                };
            
            default:
                return userInfo;
        }
    }

    async refreshToken(provider, refreshToken) {
        const providerConfig = this.providers[provider];
        if (!providerConfig) {
            throw new Error(`Unsupported OAuth2 provider: ${provider}`);
        }

        const params = new URLSearchParams({
            client_id: providerConfig.clientId,
            client_secret: providerConfig.clientSecret,
            refresh_token: refreshToken,
            grant_type: 'refresh_token'
        });

        const response = await fetch(providerConfig.tokenUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: params.toString()
        });

        if (!response.ok) {
            const error = await response.text();
            throw new Error(`Token refresh failed: ${error}`);
        }

        return await response.json();
    }

    cleanupExpiredStates() {
        const now = Date.now();
        
        for (const [state, data] of this.states.entries()) {
            if (data.expiresAt < now) {
                this.states.delete(state);
            }
        }

        for (const [nonce, data] of this.nonces.entries()) {
            if (now - data.createdAt > 600000) { // 10 minutes
                this.nonces.delete(nonce);
            }
        }
    }

    revokeToken(provider, token) {
        const revokeUrls = {
            google: 'https://oauth2.googleapis.com/revoke',
            github: null, // GitHub doesn't support token revocation
            microsoft: 'https://login.microsoftonline.com/common/oauth2/v2.0/logout'
        };

        const revokeUrl = revokeUrls[provider];
        if (!revokeUrl) {
            return Promise.resolve();
        }

        const params = new URLSearchParams({ token });
        
        return fetch(revokeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: params.toString()
        }).catch(error => {
            console.warn(`Failed to revoke token for ${provider}:`, error);
        });
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = OAuth2Provider;
} else {
    window.OAuth2Provider = OAuth2Provider;
}