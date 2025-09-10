class InputValidator {
    constructor(options = {}) {
        this.config = {
            maxStringLength: options.maxStringLength || 10000,
            maxArrayLength: options.maxArrayLength || 1000,
            maxObjectDepth: options.maxObjectDepth || 10,
            allowedTags: options.allowedTags || ['b', 'i', 'em', 'strong', 'p', 'br'],
            allowedAttributes: options.allowedAttributes || ['class', 'id'],
            enableXSSProtection: options.enableXSSProtection !== false,
            enableSQLInjectionProtection: options.enableSQLInjectionProtection !== false,
            enablePathTraversalProtection: options.enablePathTraversalProtection !== false,
            customPatterns: options.customPatterns || []
        };

        this.xssPatterns = [
            /<script[^>]*>.*?<\/script>/gi,
            /<iframe[^>]*>.*?<\/iframe>/gi,
            /<object[^>]*>.*?<\/object>/gi,
            /<embed[^>]*>/gi,
            /<link[^>]*>/gi,
            /<meta[^>]*>/gi,
            /javascript:/gi,
            /vbscript:/gi,
            /data:text\/html/gi,
            /on\w+\s*=/gi,
            /expression\s*\(/gi,
            /url\s*\(/gi,
            /@import/gi,
            /\.\.\/|\.\.\\/gi
        ];

        this.sqlPatterns = [
            /('|(\-\-)|(;)|(\||\|)|(\*|\*))/gi,
            /(union|select|insert|delete|update|drop|create|alter|exec|execute)/gi,
            /(script|javascript|vbscript|onload|onerror|onclick)/gi,
            /(or|and)\s+\d+\s*=\s*\d+/gi,
            /(having|group\s+by|order\s+by)/gi,
            /\b(waitfor|delay)\b/gi,
            /\b(sp_|xp_)\w+/gi,
            /\b(information_schema|sysobjects|syscolumns)\b/gi
        ];

        this.pathTraversalPatterns = [
            /\.\.\/|\.\.\\/g,
            /%2e%2e%2f|%2e%2e%5c/gi,
            /%252e%252e%252f|%252e%252e%255c/gi,
            /\.\.\\|\.\.\\/g,
            /%c0%ae%c0%ae%c0%af/gi,
            /%c1%9c/gi
        ];

        this.emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        this.phoneRegex = /^[\+]?[1-9]?\d{1,14}$/;
        this.urlRegex = /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;
        this.ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    }

    validateInput(input, rules = {}) {
        const result = {
            isValid: true,
            errors: [],
            sanitized: null,
            threats: []
        };

        try {
            if (input === null || input === undefined) {
                if (rules.required) {
                    result.isValid = false;
                    result.errors.push('Input is required');
                }
                return result;
            }

            const threats = this.detectThreats(input);
            if (threats.length > 0) {
                result.threats = threats;
                if (rules.blockOnThreat !== false) {
                    result.isValid = false;
                    result.errors.push(`Security threats detected: ${threats.join(', ')}`);
                    return result;
                }
            }

            result.sanitized = this.sanitizeInput(input, rules);

            if (rules.type) {
                const typeValidation = this.validateType(result.sanitized, rules.type);
                if (!typeValidation.isValid) {
                    result.isValid = false;
                    result.errors.push(...typeValidation.errors);
                }
            }

            if (rules.minLength && result.sanitized.length < rules.minLength) {
                result.isValid = false;
                result.errors.push(`Input must be at least ${rules.minLength} characters`);
            }

            if (rules.maxLength && result.sanitized.length > rules.maxLength) {
                result.isValid = false;
                result.errors.push(`Input must not exceed ${rules.maxLength} characters`);
            }

            if (rules.pattern && !rules.pattern.test(result.sanitized)) {
                result.isValid = false;
                result.errors.push('Input format is invalid');
            }

            if (rules.custom && typeof rules.custom === 'function') {
                const customResult = rules.custom(result.sanitized);
                if (!customResult.isValid) {
                    result.isValid = false;
                    result.errors.push(...customResult.errors);
                }
            }

        } catch (error) {
            result.isValid = false;
            result.errors.push(`Validation error: ${error.message}`);
        }

        return result;
    }

    detectThreats(input) {
        const threats = [];
        const inputStr = String(input);

        if (this.config.enableXSSProtection) {
            for (const pattern of this.xssPatterns) {
                if (pattern.test(inputStr)) {
                    threats.push('XSS');
                    break;
                }
            }
        }

        if (this.config.enableSQLInjectionProtection) {
            for (const pattern of this.sqlPatterns) {
                if (pattern.test(inputStr)) {
                    threats.push('SQL_INJECTION');
                    break;
                }
            }
        }

        if (this.config.enablePathTraversalProtection) {
            for (const pattern of this.pathTraversalPatterns) {
                if (pattern.test(inputStr)) {
                    threats.push('PATH_TRAVERSAL');
                    break;
                }
            }
        }

        for (const customPattern of this.config.customPatterns) {
            if (customPattern.pattern.test(inputStr)) {
                threats.push(customPattern.name || 'CUSTOM_THREAT');
            }
        }

        return [...new Set(threats)];
    }

    sanitizeInput(input, rules = {}) {
        if (typeof input !== 'string') {
            return input;
        }

        let sanitized = input;

        if (rules.trim !== false) {
            sanitized = sanitized.trim();
        }

        if (rules.removeHTML !== false) {
            sanitized = this.removeHTML(sanitized, rules.allowedTags);
        }

        if (rules.escapeHTML) {
            sanitized = this.escapeHTML(sanitized);
        }

        if (rules.removeScripts !== false) {
            sanitized = this.removeScripts(sanitized);
        }

        if (rules.normalizeWhitespace) {
            sanitized = sanitized.replace(/\s+/g, ' ');
        }

        if (rules.removeDangerousChars) {
            sanitized = sanitized.replace(/[<>"'&]/g, '');
        }

        if (rules.maxLength) {
            sanitized = sanitized.substring(0, rules.maxLength);
        }

        return sanitized;
    }

    removeHTML(input, allowedTags = []) {
        if (!allowedTags || allowedTags.length === 0) {
            return input.replace(/<[^>]*>/g, '');
        }

        const allowedTagsRegex = new RegExp(`<(?!\/?(?:${allowedTags.join('|')})\b)[^>]*>`, 'gi');
        return input.replace(allowedTagsRegex, '');
    }

    escapeHTML(input) {
        const htmlEscapes = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#x27;',
            '/': '&#x2F;'
        };

        return input.replace(/[&<>"'\/]/g, (match) => htmlEscapes[match]);
    }

    removeScripts(input) {
        return input
            .replace(/<script[^>]*>.*?<\/script>/gi, '')
            .replace(/javascript:/gi, '')
            .replace(/vbscript:/gi, '')
            .replace(/on\w+\s*=/gi, '');
    }

    validateType(input, type) {
        const result = { isValid: true, errors: [] };

        switch (type) {
            case 'email':
                if (!this.emailRegex.test(input)) {
                    result.isValid = false;
                    result.errors.push('Invalid email format');
                }
                break;

            case 'phone':
                if (!this.phoneRegex.test(input)) {
                    result.isValid = false;
                    result.errors.push('Invalid phone number format');
                }
                break;

            case 'url':
                if (!this.urlRegex.test(input)) {
                    result.isValid = false;
                    result.errors.push('Invalid URL format');
                }
                break;

            case 'ip':
                if (!this.ipRegex.test(input)) {
                    result.isValid = false;
                    result.errors.push('Invalid IP address format');
                }
                break;

            case 'number':
                if (isNaN(Number(input))) {
                    result.isValid = false;
                    result.errors.push('Input must be a number');
                }
                break;

            case 'integer':
                if (!Number.isInteger(Number(input))) {
                    result.isValid = false;
                    result.errors.push('Input must be an integer');
                }
                break;

            case 'boolean':
                if (typeof input !== 'boolean' && !['true', 'false', '1', '0'].includes(String(input).toLowerCase())) {
                    result.isValid = false;
                    result.errors.push('Input must be a boolean value');
                }
                break;

            case 'date':
                if (isNaN(Date.parse(input))) {
                    result.isValid = false;
                    result.errors.push('Invalid date format');
                }
                break;

            case 'json':
                try {
                    JSON.parse(input);
                } catch (e) {
                    result.isValid = false;
                    result.errors.push('Invalid JSON format');
                }
                break;

            case 'alphanumeric':
                if (!/^[a-zA-Z0-9]+$/.test(input)) {
                    result.isValid = false;
                    result.errors.push('Input must contain only letters and numbers');
                }
                break;

            case 'alpha':
                if (!/^[a-zA-Z]+$/.test(input)) {
                    result.isValid = false;
                    result.errors.push('Input must contain only letters');
                }
                break;

            case 'numeric':
                if (!/^[0-9]+$/.test(input)) {
                    result.isValid = false;
                    result.errors.push('Input must contain only numbers');
                }
                break;
        }

        return result;
    }

    validateObject(obj, schema) {
        const result = {
            isValid: true,
            errors: [],
            sanitized: {},
            threats: []
        };

        if (!obj || typeof obj !== 'object') {
            result.isValid = false;
            result.errors.push('Input must be an object');
            return result;
        }

        for (const [key, rules] of Object.entries(schema)) {
            const value = obj[key];
            const validation = this.validateInput(value, rules);

            if (!validation.isValid) {
                result.isValid = false;
                result.errors.push(...validation.errors.map(err => `${key}: ${err}`));
            }

            if (validation.threats.length > 0) {
                result.threats.push(...validation.threats.map(threat => `${key}: ${threat}`));
            }

            result.sanitized[key] = validation.sanitized !== null ? validation.sanitized : value;
        }

        return result;
    }

    validateArray(arr, itemRules) {
        const result = {
            isValid: true,
            errors: [],
            sanitized: [],
            threats: []
        };

        if (!Array.isArray(arr)) {
            result.isValid = false;
            result.errors.push('Input must be an array');
            return result;
        }

        if (arr.length > this.config.maxArrayLength) {
            result.isValid = false;
            result.errors.push(`Array length exceeds maximum of ${this.config.maxArrayLength}`);
            return result;
        }

        for (let i = 0; i < arr.length; i++) {
            const validation = this.validateInput(arr[i], itemRules);

            if (!validation.isValid) {
                result.isValid = false;
                result.errors.push(...validation.errors.map(err => `[${i}]: ${err}`));
            }

            if (validation.threats.length > 0) {
                result.threats.push(...validation.threats.map(threat => `[${i}]: ${threat}`));
            }

            result.sanitized.push(validation.sanitized !== null ? validation.sanitized : arr[i]);
        }

        return result;
    }

    createValidator(schema) {
        return (input) => {
            if (Array.isArray(schema)) {
                return this.validateArray(input, schema[0]);
            } else if (typeof schema === 'object') {
                return this.validateObject(input, schema);
            } else {
                return this.validateInput(input, schema);
            }
        };
    }

    addCustomPattern(name, pattern, description = '') {
        this.config.customPatterns.push({
            name,
            pattern,
            description
        });
    }

    removeCustomPattern(name) {
        this.config.customPatterns = this.config.customPatterns.filter(p => p.name !== name);
    }

    getSecurityReport() {
        return {
            timestamp: new Date().toISOString(),
            configuration: {
                xssProtection: this.config.enableXSSProtection,
                sqlInjectionProtection: this.config.enableSQLInjectionProtection,
                pathTraversalProtection: this.config.enablePathTraversalProtection,
                maxStringLength: this.config.maxStringLength,
                maxArrayLength: this.config.maxArrayLength,
                customPatterns: this.config.customPatterns.length
            },
            patterns: {
                xss: this.xssPatterns.length,
                sql: this.sqlPatterns.length,
                pathTraversal: this.pathTraversalPatterns.length,
                custom: this.config.customPatterns.length
            }
        };
    }

    updateConfiguration(newConfig) {
        Object.assign(this.config, newConfig);
        return this.config;
    }
}

class FormValidator {
    constructor(validator) {
        this.validator = validator || new InputValidator();
        this.forms = new Map();
    }

    registerForm(formId, schema) {
        this.forms.set(formId, schema);
    }

    validateForm(formId, formData) {
        const schema = this.forms.get(formId);
        if (!schema) {
            return {
                isValid: false,
                errors: [`Form schema not found for: ${formId}`],
                sanitized: null,
                threats: []
            };
        }

        return this.validator.validateObject(formData, schema);
    }

    createFormValidator(formId) {
        return (formData) => this.validateForm(formId, formData);
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { InputValidator, FormValidator };
} else {
    window.InputValidator = InputValidator;
    window.FormValidator = FormValidator;
}