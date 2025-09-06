<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Create New Project</h1>
                <a href="/myprojects" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    Back to Projects
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" id="title" name="title" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="planning" <?= (isset($_POST['status']) && $_POST['status'] === 'planning') ? 'selected' : '' ?>>Planning</option>
                            <option value="in-progress" <?= (isset($_POST['status']) && $_POST['status'] === 'in-progress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="completed" <?= (isset($_POST['status']) && $_POST['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="on-hold" <?= (isset($_POST['status']) && $_POST['status'] === 'on-hold') ? 'selected' : '' ?>>On Hold</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea id="description" name="description" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Brief description of your project..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                    <textarea id="content" name="content" rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Detailed description, features, challenges, etc..."><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="github_url" class="block text-sm font-medium text-gray-700 mb-2">GitHub URL</label>
                        <input type="url" id="github_url" name="github_url" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://github.com/username/repo"
                               value="<?= isset($_POST['github_url']) ? htmlspecialchars($_POST['github_url']) : '' ?>">
                    </div>
                    
                    <div>
                        <label for="live_url" class="block text-sm font-medium text-gray-700 mb-2">Live Demo URL</label>
                        <input type="url" id="live_url" name="live_url" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://your-project.com"
                               value="<?= isset($_POST['live_url']) ? htmlspecialchars($_POST['live_url']) : '' ?>">
                    </div>
                </div>

                <div>
                    <label for="image_url" class="block text-sm font-medium text-gray-700 mb-2">Project Image URL</label>
                    <input type="url" id="image_url" name="image_url" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="https://example.com/project-image.jpg"
                           value="<?= isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : '' ?>">
                </div>

                <div>
                    <label for="technologies" class="block text-sm font-medium text-gray-700 mb-2">Technologies</label>
                    <input type="text" id="technologies" name="technologies[]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="React, Node.js, MongoDB (comma separated)"
                           value="<?= isset($_POST['technologies']) ? htmlspecialchars(implode(', ', $_POST['technologies'])) : '' ?>">
                    <p class="text-sm text-gray-500 mt-1">Enter technologies separated by commas</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                           <?= (isset($_POST['is_featured']) && $_POST['is_featured']) ? 'checked' : '' ?>>
                    <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                        Featured Project
                    </label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="/myprojects" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.container {
    max-width: 1200px;
}

.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.transition-colors {
    transition: background-color 0.2s ease-in-out;
}

.focus\:ring-2:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

.focus\:ring-blue-500:focus {
    --tw-ring-color: rgb(59 130 246);
}

.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

.grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.gap-6 {
    gap: 1.5rem;
}

.space-y-6 > * + * {
    margin-top: 1.5rem;
}

.space-x-4 > * + * {
    margin-left: 1rem;
}

@media (min-width: 768px) {
    .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>