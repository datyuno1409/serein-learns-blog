// Shared users database - mock implementation
// In production, this should be replaced with actual database connection

let users = [
  {
    id: 1,
    username: 'admin',
    email: 'admin@example.com',
    password: '$2a$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    full_name: 'Administrator',
    role: 'admin',
    is_admin: true,
    email_verified: true,
    last_login: null,
    created_at: new Date(),
    updated_at: new Date()
  },
  {
    id: 2,
    username: 'user',
    email: 'user@example.com',
    password: '$2a$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    full_name: 'Test User',
    role: 'user',
    is_admin: false,
    email_verified: false,
    last_login: null,
    created_at: new Date(),
    updated_at: new Date()
  }
];

// Helper functions for user management
const userService = {
  // Get all users
  getAllUsers() {
    return users;
  },

  // Find user by username or email
  findUser(identifier) {
    return users.find(u => 
      u.username.toLowerCase() === identifier.toLowerCase() || 
      u.email.toLowerCase() === identifier.toLowerCase()
    );
  },

  // Find user by username
  findByUsername(username) {
    return users.find(u => u.username.toLowerCase() === username.toLowerCase());
  },

  // Find user by email
  findByEmail(email) {
    return users.find(u => u.email.toLowerCase() === email.toLowerCase());
  },

  // Find user by ID
  findById(id) {
    return users.find(u => u.id === id);
  },

  // Add new user
  addUser(userData) {
    const newUser = {
      id: users.length + 1,
      ...userData,
      created_at: new Date(),
      updated_at: new Date()
    };
    users.push(newUser);
    return newUser;
  },

  // Update user
  updateUser(id, updateData) {
    const userIndex = users.findIndex(u => u.id === id);
    if (userIndex !== -1) {
      users[userIndex] = {
        ...users[userIndex],
        ...updateData,
        updated_at: new Date()
      };
      return users[userIndex];
    }
    return null;
  },

  // Delete user
  deleteUser(id) {
    const userIndex = users.findIndex(u => u.id === id);
    if (userIndex !== -1) {
      const deletedUser = users.splice(userIndex, 1)[0];
      return deletedUser;
    }
    return null;
  },

  // Get user count
  getUserCount() {
    return users.length;
  },

  // Check if username exists
  usernameExists(username) {
    return users.some(u => u.username.toLowerCase() === username.toLowerCase());
  },

  // Check if email exists
  emailExists(email) {
    return users.some(u => u.email.toLowerCase() === email.toLowerCase());
  }
};

module.exports = userService;