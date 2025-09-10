class RoleBasedAccessControl {
    constructor() {
        this.roles = new Map();
        this.permissions = new Map();
        this.userRoles = new Map();
        this.roleHierarchy = new Map();
        this.resources = new Map();
        this.policies = new Map();
        this.auditLog = [];
        
        this.initializeDefaultRoles();
        this.initializeDefaultPermissions();
    }

    initializeDefaultRoles() {
        this.createRole('admin', 'System Administrator', {
            description: 'Full system access',
            priority: 100,
            isSystemRole: true
        });
        
        this.createRole('moderator', 'Content Moderator', {
            description: 'Content management access',
            priority: 75,
            isSystemRole: true
        });
        
        this.createRole('editor', 'Content Editor', {
            description: 'Content creation and editing',
            priority: 50,
            isSystemRole: true
        });
        
        this.createRole('user', 'Regular User', {
            description: 'Basic user access',
            priority: 25,
            isSystemRole: true
        });
        
        this.createRole('guest', 'Guest User', {
            description: 'Limited read-only access',
            priority: 10,
            isSystemRole: true
        });

        this.setRoleHierarchy('admin', ['moderator', 'editor', 'user', 'guest']);
        this.setRoleHierarchy('moderator', ['editor', 'user', 'guest']);
        this.setRoleHierarchy('editor', ['user', 'guest']);
        this.setRoleHierarchy('user', ['guest']);
    }

    initializeDefaultPermissions() {
        const permissions = [
            { name: 'read', description: 'Read access to resources' },
            { name: 'write', description: 'Write access to resources' },
            { name: 'delete', description: 'Delete access to resources' },
            { name: 'admin', description: 'Administrative access' },
            { name: 'moderate', description: 'Moderation capabilities' },
            { name: 'publish', description: 'Publishing capabilities' },
            { name: 'comment', description: 'Commenting capabilities' },
            { name: 'upload', description: 'File upload capabilities' },
            { name: 'manage_users', description: 'User management' },
            { name: 'manage_content', description: 'Content management' },
            { name: 'view_analytics', description: 'Analytics viewing' },
            { name: 'system_config', description: 'System configuration' }
        ];

        permissions.forEach(perm => {
            this.createPermission(perm.name, perm.description);
        });

        this.assignPermissionsToRole('admin', [
            'read', 'write', 'delete', 'admin', 'moderate', 'publish',
            'comment', 'upload', 'manage_users', 'manage_content',
            'view_analytics', 'system_config'
        ]);
        
        this.assignPermissionsToRole('moderator', [
            'read', 'write', 'delete', 'moderate', 'publish',
            'comment', 'upload', 'manage_content', 'view_analytics'
        ]);
        
        this.assignPermissionsToRole('editor', [
            'read', 'write', 'publish', 'comment', 'upload', 'manage_content'
        ]);
        
        this.assignPermissionsToRole('user', [
            'read', 'comment', 'upload'
        ]);
        
        this.assignPermissionsToRole('guest', [
            'read'
        ]);
    }

    createRole(name, displayName, options = {}) {
        if (this.roles.has(name)) {
            throw new Error(`Role '${name}' already exists`);
        }

        const role = {
            name,
            displayName,
            description: options.description || '',
            permissions: new Set(),
            priority: options.priority || 0,
            isSystemRole: options.isSystemRole || false,
            isActive: options.isActive !== false,
            createdAt: new Date().toISOString(),
            updatedAt: new Date().toISOString(),
            metadata: options.metadata || {}
        };

        this.roles.set(name, role);
        this.logAudit('role_created', { roleName: name, displayName });
        return role;
    }

    updateRole(name, updates) {
        const role = this.roles.get(name);
        if (!role) {
            throw new Error(`Role '${name}' not found`);
        }

        if (role.isSystemRole && (updates.name || updates.isSystemRole === false)) {
            throw new Error('Cannot modify system role properties');
        }

        Object.assign(role, updates, { updatedAt: new Date().toISOString() });
        this.logAudit('role_updated', { roleName: name, updates });
        return role;
    }

    deleteRole(name) {
        const role = this.roles.get(name);
        if (!role) {
            throw new Error(`Role '${name}' not found`);
        }

        if (role.isSystemRole) {
            throw new Error('Cannot delete system role');
        }

        for (const [userId, userRoles] of this.userRoles.entries()) {
            userRoles.delete(name);
        }

        this.roles.delete(name);
        this.roleHierarchy.delete(name);
        
        for (const [parentRole, children] of this.roleHierarchy.entries()) {
            const index = children.indexOf(name);
            if (index > -1) {
                children.splice(index, 1);
            }
        }

        this.logAudit('role_deleted', { roleName: name });
        return true;
    }

    createPermission(name, description = '', options = {}) {
        if (this.permissions.has(name)) {
            throw new Error(`Permission '${name}' already exists`);
        }

        const permission = {
            name,
            description,
            category: options.category || 'general',
            isSystemPermission: options.isSystemPermission || false,
            createdAt: new Date().toISOString(),
            metadata: options.metadata || {}
        };

        this.permissions.set(name, permission);
        this.logAudit('permission_created', { permissionName: name, description });
        return permission;
    }

    assignPermissionsToRole(roleName, permissions) {
        const role = this.roles.get(roleName);
        if (!role) {
            throw new Error(`Role '${roleName}' not found`);
        }

        const validPermissions = permissions.filter(perm => this.permissions.has(perm));
        validPermissions.forEach(perm => role.permissions.add(perm));
        
        role.updatedAt = new Date().toISOString();
        this.logAudit('permissions_assigned', { roleName, permissions: validPermissions });
        return validPermissions;
    }

    revokePermissionsFromRole(roleName, permissions) {
        const role = this.roles.get(roleName);
        if (!role) {
            throw new Error(`Role '${roleName}' not found`);
        }

        permissions.forEach(perm => role.permissions.delete(perm));
        role.updatedAt = new Date().toISOString();
        this.logAudit('permissions_revoked', { roleName, permissions });
        return true;
    }

    assignRoleToUser(userId, roleName) {
        if (!this.roles.has(roleName)) {
            throw new Error(`Role '${roleName}' not found`);
        }

        if (!this.userRoles.has(userId)) {
            this.userRoles.set(userId, new Set());
        }

        this.userRoles.get(userId).add(roleName);
        this.logAudit('role_assigned', { userId, roleName });
        return true;
    }

    revokeRoleFromUser(userId, roleName) {
        const userRoles = this.userRoles.get(userId);
        if (!userRoles) {
            return false;
        }

        const removed = userRoles.delete(roleName);
        if (removed) {
            this.logAudit('role_revoked', { userId, roleName });
        }
        return removed;
    }

    setRoleHierarchy(parentRole, childRoles) {
        if (!this.roles.has(parentRole)) {
            throw new Error(`Parent role '${parentRole}' not found`);
        }

        const validChildRoles = childRoles.filter(role => this.roles.has(role));
        this.roleHierarchy.set(parentRole, validChildRoles);
        this.logAudit('hierarchy_set', { parentRole, childRoles: validChildRoles });
        return true;
    }

    getUserRoles(userId) {
        return Array.from(this.userRoles.get(userId) || []);
    }

    getUserPermissions(userId) {
        const userRoles = this.getUserRoles(userId);
        const permissions = new Set();

        userRoles.forEach(roleName => {
            const role = this.roles.get(roleName);
            if (role && role.isActive) {
                role.permissions.forEach(perm => permissions.add(perm));
                
                const inheritedRoles = this.getInheritedRoles(roleName);
                inheritedRoles.forEach(inheritedRole => {
                    const inheritedRoleObj = this.roles.get(inheritedRole);
                    if (inheritedRoleObj && inheritedRoleObj.isActive) {
                        inheritedRoleObj.permissions.forEach(perm => permissions.add(perm));
                    }
                });
            }
        });

        return Array.from(permissions);
    }

    getInheritedRoles(roleName) {
        const inherited = new Set();
        const queue = [roleName];
        const visited = new Set();

        while (queue.length > 0) {
            const currentRole = queue.shift();
            if (visited.has(currentRole)) continue;
            
            visited.add(currentRole);
            const children = this.roleHierarchy.get(currentRole) || [];
            
            children.forEach(child => {
                inherited.add(child);
                queue.push(child);
            });
        }

        return Array.from(inherited);
    }

    hasPermission(userId, permission, resource = null, context = {}) {
        const userPermissions = this.getUserPermissions(userId);
        
        if (!userPermissions.includes(permission)) {
            this.logAudit('access_denied', { userId, permission, resource, reason: 'permission_not_found' });
            return false;
        }

        if (resource && this.resources.has(resource)) {
            const resourceConfig = this.resources.get(resource);
            if (resourceConfig.requiresOwnership && context.ownerId !== userId) {
                const userRoles = this.getUserRoles(userId);
                const hasAdminRole = userRoles.some(role => {
                    const roleObj = this.roles.get(role);
                    return roleObj && roleObj.priority >= 75; // moderator level or higher
                });
                
                if (!hasAdminRole) {
                    this.logAudit('access_denied', { userId, permission, resource, reason: 'ownership_required' });
                    return false;
                }
            }
        }

        const policyResult = this.evaluatePolicies(userId, permission, resource, context);
        if (!policyResult.allowed) {
            this.logAudit('access_denied', { userId, permission, resource, reason: 'policy_violation', policy: policyResult.policy });
            return false;
        }

        this.logAudit('access_granted', { userId, permission, resource });
        return true;
    }

    hasRole(userId, roleName) {
        const userRoles = this.getUserRoles(userId);
        return userRoles.includes(roleName) || this.hasInheritedRole(userId, roleName);
    }

    hasInheritedRole(userId, roleName) {
        const userRoles = this.getUserRoles(userId);
        
        for (const userRole of userRoles) {
            const inheritedRoles = this.getInheritedRoles(userRole);
            if (inheritedRoles.includes(roleName)) {
                return true;
            }
        }
        
        return false;
    }

    defineResource(name, config = {}) {
        this.resources.set(name, {
            name,
            requiresOwnership: config.requiresOwnership || false,
            allowedOperations: config.allowedOperations || ['read', 'write', 'delete'],
            metadata: config.metadata || {}
        });
    }

    createPolicy(name, condition, options = {}) {
        this.policies.set(name, {
            name,
            condition,
            priority: options.priority || 0,
            isActive: options.isActive !== false,
            description: options.description || '',
            createdAt: new Date().toISOString()
        });
    }

    evaluatePolicies(userId, permission, resource, context) {
        const applicablePolicies = Array.from(this.policies.values())
            .filter(policy => policy.isActive)
            .sort((a, b) => b.priority - a.priority);

        for (const policy of applicablePolicies) {
            try {
                const result = policy.condition(userId, permission, resource, context, this);
                if (result === false) {
                    return { allowed: false, policy: policy.name };
                }
            } catch (error) {
                console.warn(`Policy evaluation error for '${policy.name}':`, error);
            }
        }

        return { allowed: true };
    }

    logAudit(action, details) {
        const auditEntry = {
            id: this.generateId(),
            action,
            details,
            timestamp: new Date().toISOString(),
            userAgent: typeof navigator !== 'undefined' ? navigator.userAgent : null
        };

        this.auditLog.push(auditEntry);
        
        if (this.auditLog.length > 10000) {
            this.auditLog = this.auditLog.slice(-5000);
        }
    }

    getAuditLog(filters = {}) {
        let logs = [...this.auditLog];

        if (filters.action) {
            logs = logs.filter(log => log.action === filters.action);
        }

        if (filters.userId) {
            logs = logs.filter(log => log.details.userId === filters.userId);
        }

        if (filters.startDate) {
            logs = logs.filter(log => new Date(log.timestamp) >= new Date(filters.startDate));
        }

        if (filters.endDate) {
            logs = logs.filter(log => new Date(log.timestamp) <= new Date(filters.endDate));
        }

        return logs.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    }

    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    exportConfiguration() {
        return {
            roles: Array.from(this.roles.entries()).map(([name, role]) => ({
                ...role,
                permissions: Array.from(role.permissions)
            })),
            permissions: Array.from(this.permissions.entries()),
            roleHierarchy: Array.from(this.roleHierarchy.entries()),
            resources: Array.from(this.resources.entries()),
            policies: Array.from(this.policies.entries()).map(([name, policy]) => ({
                ...policy,
                condition: policy.condition.toString()
            }))
        };
    }

    importConfiguration(config) {
        if (config.roles) {
            config.roles.forEach(role => {
                const permissions = new Set(role.permissions);
                this.roles.set(role.name, { ...role, permissions });
            });
        }

        if (config.permissions) {
            config.permissions.forEach(([name, permission]) => {
                this.permissions.set(name, permission);
            });
        }

        if (config.roleHierarchy) {
            config.roleHierarchy.forEach(([parent, children]) => {
                this.roleHierarchy.set(parent, children);
            });
        }

        if (config.resources) {
            config.resources.forEach(([name, resource]) => {
                this.resources.set(name, resource);
            });
        }

        if (config.policies) {
            config.policies.forEach(policy => {
                try {
                    const condition = new Function('userId', 'permission', 'resource', 'context', 'rbac', `return (${policy.condition})`);
                    this.policies.set(policy.name, { ...policy, condition });
                } catch (error) {
                    console.warn(`Failed to import policy '${policy.name}':`, error);
                }
            });
        }
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = RoleBasedAccessControl;
} else {
    window.RoleBasedAccessControl = RoleBasedAccessControl;
}