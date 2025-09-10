// Security System Demo
// This file demonstrates how to use the comprehensive security system

import { SecurityManager } from '../security-manager.js';
import { SecurityTester } from '../testing/security-tester.js';

class SecurityDemo {
    constructor() {
        this.securityManager = null;
        this.securityTester = null;
        this.demoResults = [];
    }

    async initialize() {
        console.log('🔐 Initializing Security System Demo...');
        
        try {
            // Initialize Security Manager with demo configuration
            this.securityManager = new SecurityManager({
                environment: 'development',
                authentication: {
                    jwtSecret: 'demo-jwt-secret-key-with-sufficient-length-for-security',
                    jwtExpiresIn: '1h',
                    enableOAuth2: true,
                    oauth2Providers: {
                        google: {
                            clientId: 'demo-google-client-id',
                            clientSecret: 'demo-google-client-secret',
                            redirectUri: 'http://localhost:8000/auth/google/callback'
                        }
                    }
                },
                encryption: {
                    algorithm: 'aes-256-gcm',
                    keyDerivation: 'pbkdf2',
                    iterations: 100000
                },
                monitoring: {
                    enableThreatDetection: true,
                    bruteForceThreshold: 5,
                    anomalyDetectionEnabled: true
                },
                validation: {
                    enableThreatDetection: true,
                    sanitizeInput: true
                }
            });

            await this.securityManager.initialize();
            
            // Initialize Security Tester
            this.securityTester = new SecurityTester(this.securityManager, {
                enablePenetrationTesting: true,
                enableVulnerabilityScanning: true,
                enableComplianceChecking: true
            });

            console.log('✅ Security System initialized successfully');
            return true;
        } catch (error) {
            console.error('❌ Failed to initialize security system:', error);
            return false;
        }
    }

    async runAuthenticationDemo() {
        console.log('\n🔑 Running Authentication Demo...');
        
        try {
            // Demo user registration
            const registrationResult = await this.securityManager.authenticateUser({
                action: 'register',
                username: 'demo-user',
                email: 'demo@example.com',
                password: 'SecurePassword123!',
                request: {
                    ip: '192.168.1.100',
                    headers: { 'user-agent': 'SecurityDemo/1.0' }
                }
            });

            console.log('📝 User Registration:', registrationResult.success ? '✅ Success' : '❌ Failed');
            this.demoResults.push({ demo: 'authentication-register', success: registrationResult.success });

            // Demo user login
            const loginResult = await this.securityManager.authenticateUser({
                action: 'login',
                username: 'demo-user',
                password: 'SecurePassword123!',
                request: {
                    ip: '192.168.1.100',
                    headers: { 'user-agent': 'SecurityDemo/1.0' }
                }
            });

            console.log('🔓 User Login:', loginResult.success ? '✅ Success' : '❌ Failed');
            if (loginResult.success && loginResult.token) {
                console.log('🎫 JWT Token generated:', loginResult.token.substring(0, 20) + '...');
            }
            this.demoResults.push({ demo: 'authentication-login', success: loginResult.success });

            // Demo brute force protection
            console.log('🛡️ Testing Brute Force Protection...');
            let blockedAttempt = false;
            for (let i = 0; i < 7; i++) {
                try {
                    await this.securityManager.authenticateUser({
                        action: 'login',
                        username: 'demo-user',
                        password: 'wrong-password',
                        request: {
                            ip: '192.168.1.200',
                            headers: { 'user-agent': 'SecurityDemo/1.0' }
                        }
                    });
                } catch (error) {
                    if (error.message.includes('blocked') || error.message.includes('locked')) {
                        console.log(`🚫 Brute force protection activated after ${i + 1} attempts`);
                        blockedAttempt = true;
                        break;
                    }
                }
            }
            this.demoResults.push({ demo: 'brute-force-protection', success: blockedAttempt });

        } catch (error) {
            console.error('❌ Authentication demo failed:', error.message);
            this.demoResults.push({ demo: 'authentication', success: false, error: error.message });
        }
    }

    async runEncryptionDemo() {
        console.log('\n🔒 Running Encryption Demo...');
        
        try {
            const sensitiveData = 'Credit Card: 4532-1234-5678-9012, SSN: 123-45-6789';
            
            // Encrypt sensitive data
            const encrypted = await this.securityManager.encryptSensitiveData(sensitiveData, {
                password: 'encryption-demo-password'
            });
            
            console.log('🔐 Data Encryption:', encrypted ? '✅ Success' : '❌ Failed');
            console.log('📊 Original length:', sensitiveData.length, 'Encrypted length:', encrypted.length);
            
            // Decrypt data
            const decrypted = await this.securityManager.decryptSensitiveData(encrypted, {
                password: 'encryption-demo-password'
            });
            
            const decryptionSuccess = decrypted === sensitiveData;
            console.log('🔓 Data Decryption:', decryptionSuccess ? '✅ Success' : '❌ Failed');
            
            // Test wrong password
            try {
                await this.securityManager.decryptSensitiveData(encrypted, {
                    password: 'wrong-password'
                });
                console.log('🚨 Wrong password test: ❌ Failed (should have been rejected)');
                this.demoResults.push({ demo: 'encryption-wrong-password', success: false });
            } catch (error) {
                console.log('🛡️ Wrong password correctly rejected: ✅ Success');
                this.demoResults.push({ demo: 'encryption-wrong-password', success: true });
            }
            
            this.demoResults.push({ 
                demo: 'encryption', 
                success: encrypted && decryptionSuccess,
                details: { originalLength: sensitiveData.length, encryptedLength: encrypted.length }
            });

        } catch (error) {
            console.error('❌ Encryption demo failed:', error.message);
            this.demoResults.push({ demo: 'encryption', success: false, error: error.message });
        }
    }

    async runAccessControlDemo() {
        console.log('\n🔐 Running Access Control Demo...');
        
        try {
            // Test different access scenarios
            const accessTests = [
                { user: 'admin-user', resource: 'admin-panel', action: 'read', expected: true },
                { user: 'regular-user', resource: 'user-profile', action: 'read', expected: true },
                { user: 'regular-user', resource: 'admin-panel', action: 'read', expected: false },
                { user: 'guest-user', resource: 'public-content', action: 'read', expected: true },
                { user: 'guest-user', resource: 'user-profile', action: 'write', expected: false }
            ];

            let correctAccessControls = 0;
            
            for (const test of accessTests) {
                try {
                    const hasAccess = await this.securityManager.authorizeAccess(
                        test.user, 
                        test.resource, 
                        test.action
                    );
                    
                    const isCorrect = hasAccess === test.expected;
                    console.log(`👤 ${test.user} → ${test.resource} (${test.action}): ${hasAccess ? '✅ Allowed' : '❌ Denied'} ${isCorrect ? '(Correct)' : '(Incorrect)'}`);
                    
                    if (isCorrect) correctAccessControls++;
                } catch (error) {
                    const isCorrect = !test.expected;
                    console.log(`👤 ${test.user} → ${test.resource} (${test.action}): ❌ Error ${isCorrect ? '(Expected)' : '(Unexpected)'}`);
                    if (isCorrect) correctAccessControls++;
                }
            }
            
            console.log(`🎯 Access Control Accuracy: ${correctAccessControls}/${accessTests.length} (${Math.round(correctAccessControls/accessTests.length*100)}%)`);
            this.demoResults.push({ 
                demo: 'access-control', 
                success: correctAccessControls >= accessTests.length * 0.8,
                details: { correct: correctAccessControls, total: accessTests.length }
            });

        } catch (error) {
            console.error('❌ Access control demo failed:', error.message);
            this.demoResults.push({ demo: 'access-control', success: false, error: error.message });
        }
    }

    async runInputValidationDemo() {
        console.log('\n🛡️ Running Input Validation Demo...');
        
        try {
            const maliciousInputs = [
                { input: "'; DROP TABLE users; --", type: 'sql-injection' },
                { input: '<script>alert("XSS")</script>', type: 'xss' },
                { input: '../../../etc/passwd', type: 'path-traversal' },
                { input: 'user@example.com', type: 'safe-email' },
                { input: 'Normal text input', type: 'safe-text' }
            ];

            let threatsDetected = 0;
            let safeInputsAccepted = 0;
            
            for (const testInput of maliciousInputs) {
                const validation = this.securityManager.validateInput(testInput.input, {
                    type: 'string',
                    enableThreatDetection: true
                });
                
                const isThreat = validation.threats && validation.threats.length > 0;
                const isSafe = testInput.type.startsWith('safe-');
                
                if (isThreat && !isSafe) {
                    threatsDetected++;
                    console.log(`🚨 Threat detected (${testInput.type}): ✅ Blocked`);
                } else if (!isThreat && isSafe) {
                    safeInputsAccepted++;
                    console.log(`✅ Safe input (${testInput.type}): ✅ Accepted`);
                } else if (isThreat && isSafe) {
                    console.log(`⚠️ False positive (${testInput.type}): ❌ Safe input blocked`);
                } else {
                    console.log(`🚨 Threat missed (${testInput.type}): ❌ Malicious input accepted`);
                }
            }
            
            const maliciousInputs_count = maliciousInputs.filter(i => !i.type.startsWith('safe-')).length;
            const safeInputs_count = maliciousInputs.filter(i => i.type.startsWith('safe-')).length;
            
            console.log(`🎯 Threat Detection: ${threatsDetected}/${maliciousInputs_count} threats detected`);
            console.log(`✅ Safe Input Handling: ${safeInputsAccepted}/${safeInputs_count} safe inputs accepted`);
            
            this.demoResults.push({ 
                demo: 'input-validation', 
                success: threatsDetected >= maliciousInputs_count * 0.8 && safeInputsAccepted >= safeInputs_count * 0.8,
                details: { threatsDetected, maliciousInputs_count, safeInputsAccepted, safeInputs_count }
            });

        } catch (error) {
            console.error('❌ Input validation demo failed:', error.message);
            this.demoResults.push({ demo: 'input-validation', success: false, error: error.message });
        }
    }

    async runSecurityHeadersDemo() {
        console.log('\n🛡️ Running Security Headers Demo...');
        
        try {
            const mockResponse = { headers: {} };
            const mockRequest = { 
                secure: true, 
                headers: { host: 'localhost:8000' },
                url: '/demo'
            };
            
            const responseWithHeaders = this.securityManager.applySecurityHeaders(mockResponse, mockRequest);
            
            const expectedHeaders = [
                'X-Content-Type-Options',
                'X-Frame-Options', 
                'X-XSS-Protection',
                'Strict-Transport-Security',
                'Content-Security-Policy',
                'Referrer-Policy'
            ];
            
            let headersApplied = 0;
            
            console.log('📋 Security Headers Applied:');
            expectedHeaders.forEach(header => {
                if (responseWithHeaders.headers && responseWithHeaders.headers[header]) {
                    console.log(`  ✅ ${header}: ${responseWithHeaders.headers[header]}`);
                    headersApplied++;
                } else {
                    console.log(`  ❌ ${header}: Not set`);
                }
            });
            
            console.log(`🎯 Headers Coverage: ${headersApplied}/${expectedHeaders.length} (${Math.round(headersApplied/expectedHeaders.length*100)}%)`);
            
            this.demoResults.push({ 
                demo: 'security-headers', 
                success: headersApplied >= expectedHeaders.length * 0.8,
                details: { applied: headersApplied, total: expectedHeaders.length }
            });

        } catch (error) {
            console.error('❌ Security headers demo failed:', error.message);
            this.demoResults.push({ demo: 'security-headers', success: false, error: error.message });
        }
    }

    async runMonitoringDemo() {
        console.log('\n📊 Running Security Monitoring Demo...');
        
        try {
            // Simulate various security events
            const securityEvents = [
                { type: 'suspicious-login', data: { ip: '192.168.1.200', attempts: 3 } },
                { type: 'anomalous-access', data: { userId: 'user123', resource: 'admin-panel' } },
                { type: 'data-access', data: { userId: 'user456', sensitiveData: true } }
            ];

            let eventsProcessed = 0;
            
            for (const event of securityEvents) {
                try {
                    // Log security event
                    this.securityManager.logSecurityEvent(event.type, event.data);
                    
                    // Check if monitoring system processes the event
                    console.log(`📝 Security Event Logged: ${event.type} ✅`);
                    eventsProcessed++;
                } catch (error) {
                    console.log(`📝 Security Event Failed: ${event.type} ❌`);
                }
            }
            
            // Test threat detection
            const threatDetectionTest = await this.testThreatDetection();
            
            console.log(`📊 Events Processed: ${eventsProcessed}/${securityEvents.length}`);
            console.log(`🔍 Threat Detection: ${threatDetectionTest ? '✅ Working' : '❌ Failed'}`);
            
            this.demoResults.push({ 
                demo: 'monitoring', 
                success: eventsProcessed >= securityEvents.length * 0.8 && threatDetectionTest,
                details: { eventsProcessed, totalEvents: securityEvents.length, threatDetection: threatDetectionTest }
            });

        } catch (error) {
            console.error('❌ Monitoring demo failed:', error.message);
            this.demoResults.push({ demo: 'monitoring', success: false, error: error.message });
        }
    }

    async testThreatDetection() {
        try {
            // Test SQL injection detection
            const sqlInjectionTest = this.securityManager.validateInput("'; DROP TABLE users; --", {
                enableThreatDetection: true
            });
            
            return sqlInjectionTest.threats && sqlInjectionTest.threats.includes('SQL_INJECTION');
        } catch (error) {
            return false;
        }
    }

    async runSecurityTesting() {
        console.log('\n🧪 Running Comprehensive Security Testing...');
        
        try {
            const testReport = await this.securityTester.runAllTests();
            
            console.log('📊 Security Test Results:');
            console.log(`  🎯 Security Score: ${testReport.summary.securityScore}%`);
            console.log(`  ✅ Tests Passed: ${testReport.summary.passedTests}`);
            console.log(`  ❌ Tests Failed: ${testReport.summary.failedTests}`);
            console.log(`  ⚠️ Risk Level: ${testReport.summary.riskLevel.toUpperCase()}`);
            
            if (testReport.vulnerabilities.length > 0) {
                console.log('\n🚨 Critical Vulnerabilities Found:');
                testReport.vulnerabilities.forEach(vuln => {
                    console.log(`  - ${vuln.type}: ${vuln.description}`);
                });
            }
            
            if (testReport.recommendations.length > 0) {
                console.log('\n💡 Security Recommendations:');
                testReport.recommendations.forEach(rec => {
                    console.log(`  - [${rec.priority.toUpperCase()}] ${rec.title}`);
                });
            }
            
            this.demoResults.push({ 
                demo: 'security-testing', 
                success: testReport.summary.securityScore >= 70,
                details: testReport.summary
            });
            
            return testReport;

        } catch (error) {
            console.error('❌ Security testing failed:', error.message);
            this.demoResults.push({ demo: 'security-testing', success: false, error: error.message });
            return null;
        }
    }

    async runFullDemo() {
        console.log('🚀 Starting Comprehensive Security System Demo\n');
        console.log('=' .repeat(60));
        
        const initialized = await this.initialize();
        if (!initialized) {
            console.log('❌ Demo failed to initialize');
            return;
        }

        // Run all demo modules
        await this.runAuthenticationDemo();
        await this.runEncryptionDemo();
        await this.runAccessControlDemo();
        await this.runInputValidationDemo();
        await this.runSecurityHeadersDemo();
        await this.runMonitoringDemo();
        
        // Run comprehensive security testing
        const testReport = await this.runSecurityTesting();
        
        // Generate final report
        this.generateFinalReport(testReport);
    }

    generateFinalReport(testReport) {
        console.log('\n' + '=' .repeat(60));
        console.log('📋 FINAL SECURITY DEMO REPORT');
        console.log('=' .repeat(60));
        
        const successfulDemos = this.demoResults.filter(r => r.success).length;
        const totalDemos = this.demoResults.length;
        const successRate = Math.round((successfulDemos / totalDemos) * 100);
        
        console.log(`\n📊 Demo Summary:`);
        console.log(`  🎯 Success Rate: ${successRate}% (${successfulDemos}/${totalDemos})`);
        
        console.log(`\n📋 Demo Results:`);
        this.demoResults.forEach(result => {
            const status = result.success ? '✅' : '❌';
            console.log(`  ${status} ${result.demo}: ${result.success ? 'PASSED' : 'FAILED'}`);
            if (result.error) {
                console.log(`      Error: ${result.error}`);
            }
        });
        
        if (testReport) {
            console.log(`\n🧪 Security Testing Summary:`);
            console.log(`  🎯 Security Score: ${testReport.summary.securityScore}%`);
            console.log(`  ⚠️ Risk Level: ${testReport.summary.riskLevel.toUpperCase()}`);
            console.log(`  🔍 Tests: ${testReport.summary.passedTests} passed, ${testReport.summary.failedTests} failed`);
        }
        
        console.log(`\n💡 Overall Assessment:`);
        if (successRate >= 90) {
            console.log('  🟢 EXCELLENT: Security system is working exceptionally well');
        } else if (successRate >= 75) {
            console.log('  🟡 GOOD: Security system is working well with minor issues');
        } else if (successRate >= 50) {
            console.log('  🟠 FAIR: Security system needs improvement');
        } else {
            console.log('  🔴 POOR: Security system requires immediate attention');
        }
        
        console.log(`\n🎉 Security Demo Completed!`);
        console.log('=' .repeat(60));
    }

    // Method to run individual demo components
    async runDemo(demoName) {
        const initialized = await this.initialize();
        if (!initialized) return;

        switch (demoName.toLowerCase()) {
            case 'auth':
            case 'authentication':
                await this.runAuthenticationDemo();
                break;
            case 'encryption':
                await this.runEncryptionDemo();
                break;
            case 'access':
            case 'access-control':
                await this.runAccessControlDemo();
                break;
            case 'validation':
            case 'input-validation':
                await this.runInputValidationDemo();
                break;
            case 'headers':
            case 'security-headers':
                await this.runSecurityHeadersDemo();
                break;
            case 'monitoring':
                await this.runMonitoringDemo();
                break;
            case 'testing':
            case 'security-testing':
                await this.runSecurityTesting();
                break;
            case 'all':
            case 'full':
                await this.runFullDemo();
                break;
            default:
                console.log('❌ Unknown demo:', demoName);
                console.log('Available demos: auth, encryption, access, validation, headers, monitoring, testing, all');
        }
    }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { SecurityDemo };
} else {
    window.SecurityDemo = SecurityDemo;
}

// Auto-run demo if this file is executed directly
if (typeof require !== 'undefined' && require.main === module) {
    const demo = new SecurityDemo();
    
    // Get demo name from command line arguments
    const demoName = process.argv[2] || 'all';
    
    demo.runDemo(demoName).catch(error => {
        console.error('Demo failed:', error);
        process.exit(1);
    });
}