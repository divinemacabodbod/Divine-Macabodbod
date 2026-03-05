# Authorization Guide with Spatie Laravel Permissions

This guide demonstrates how to implement role-based and permission-based authorization in your Laravel application using **Spatie Laravel Permissions**.

## Table of Contents
1. [Overview](#overview)
2. [Roles & Permissions Structure](#roles--permissions-structure)
3. [Authorization Methods](#authorization-methods)
4. [Policy-Based Authorization](#policy-based-authorization)
5. [Middleware Authorization](#middleware-authorization)
6. [Practical Examples](#practical-examples)
7. [Testing Authorization](#testing-authorization)

---

## Overview

**Spatie Laravel Permissions** provides three ways to implement authorization:

1. **Policies** - Model-based authorization (recommended for RESTful APIs)
2. **Middleware** - Route-level authorization
3. **Gates** - Manual permission checks in code/views

---

## Roles & Permissions Structure

### Created Roles

| Role | Permissions | Use Case |
|------|-------------|----------|
| **super-admin** | All permissions | Full system access |
| **admin** | User management, settings, reports, analytics | Administrative tasks |
| **editor** | Create/edit/delete posts & comments, publish | Content management |
| **author** | Create/edit/read posts, comment | Content creation |
| **contributor** | Create comments, read posts | Limited participation |
| **user** | Read posts & comments | Basic user access |

### Permission Groups

**User Permissions:**
- `create users`
- `read users`
- `update users`
- `delete users`
- `list users`

**Post Permissions:**
- `create posts`
- `read posts`
- `update posts`
- `delete posts`
- `list posts`
- `publish posts`

**Comment Permissions:**
- `create comments`
- `read comments`
- `update comments`
- `delete comments`
- `list comments`

**Admin Permissions:**
- `view analytics`
- `view reports`
- `manage settings`
- `manage roles`
- `manage permissions`

---

## Authorization Methods

### 1. Using Laravel's `can()` Method

Check if user has permission:

```php
if ($user->can('edit posts')) {
    // User has permission
}
```

### 2. Using Blade Directives

In your views:

```blade
@can('edit posts')
    <a href="{{ route('posts.edit', $post) }}">Edit</a>
@endcan

@cannot('delete posts')
    <p>You cannot delete posts</p>
@endcannot

@role('admin')
    <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
@endrole
```

### 3. Direct Method Checks

```php
// Check permission
$user->hasPermissionTo('edit posts');

// Check role
$user->hasRole('editor');
$user->hasAnyRole('editor', 'admin');
$user->hasAllRoles('editor', 'admin');

// Assign role
$user->assignRole('editor');

// Revoke role
$user->removeRole('editor');

// Create permission at runtime
Permission::create(['name' => 'new-permission']);
```

---

## Policy-Based Authorization

### What is a Policy?

A **Policy** is a class that defines authorization logic for model actions. It's the recommended approach for RESTful APIs.

### PostPolicy Example

Location: `app/Policies/PostPolicy.php`

```php
public function viewAny(User $user): bool
{
    return $user->hasPermissionTo('list posts');
}

public function view(User $user, Post $post): bool
{
    return $user->hasPermissionTo('read posts');
}

public function create(User $user): bool
{
    return $user->hasPermissionTo('create posts');
}

public function update(User $user, Post $post): bool
{
    return ($user->id === $post->user_id || $user->hasRole('admin')) 
        && $user->hasPermissionTo('update posts');
}

public function delete(User $user, Post $post): bool
{
    return ($user->id === $post->user_id || $user->hasRole('admin')) 
        && $user->hasPermissionTo('delete posts');
}

public function publish(User $user, Post $post): bool
{
    return $user->hasPermissionTo('publish posts');
}
```

### Using Policy in Controller

Location: `app/Http/Controllers/PostController.php`

```php
class PostController extends Controller
{
    // List posts - Check if user can view any posts
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Post::class);
        $posts = Post::paginate(15);
        return response()->json($posts);
    }

    // Create post - Check if user can create posts
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Post::class);
        $post = Post::create($request->validated());
        return response()->json($post, 201);
    }

    // Update post - Check if user can update this post
    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);
        $post->update($request->validated());
        return response()->json($post);
    }

    // Delete post - Check if user can delete this post
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // Custom action - Check if user can publish
    public function publish(Post $post): JsonResponse
    {
        $this->authorize('publish', $post);
        $post->update(['status' => 'published']);
        return response()->json($post);
    }
}
```

### Authorization Responses

Return meaningful messages:

```php
use Illuminate\Auth\Access\Response;

public function update(User $user, Post $post): Response
{
    if ($user->id === $post->user_id) {
        return Response::allow();
    }

    return Response::deny('You cannot edit other users\' posts.');
}
```

---

## Middleware Authorization

### CheckPermission Middleware

Location: `app/Http/Middleware/CheckPermission.php`

Checks if user has specific permission before accessing route:

```php
class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!empty($permissions)) {
            $hasPermission = false;
            foreach ($permissions as $permission) {
                if ($request->user()->hasPermissionTo($permission) 
                    || $request->user()->hasRole($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
        }

        return $next($request);
    }
}
```

### Using in Routes

```php
Route::post('/users', [UserController::class, 'store'])
    ->middleware('auth:sanctum', 'check_permission:create users');

Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->middleware('auth:sanctum', 'check_permission:delete users');

// Multiple permissions - user needs one of them
Route::put('/posts/{post}', [PostController::class, 'update'])
    ->middleware('auth:sanctum', 'check_permission:update posts,editor');

// Check role
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware('auth:sanctum', 'check_permission:admin');
```

---

## Practical Examples

### Example 1: Create a New Post (Author)

**User Details:**
- Role: `author`
- Permissions: `create posts`, `read posts`, etc.

**Request:**
```http
POST /api/posts HTTP/1.1
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "My First Post",
    "content": "This is my content"
}
```

**Controller Flow:**
```php
public function store(Request $request): JsonResponse
{
    // Policy check: Can user create posts?
    $this->authorize('create', Post::class);
    
    // User has 'create posts' permission ✓
    $post = Post::create([
        'title' => $request->title,
        'content' => $request->content,
        'user_id' => auth()->id(),
    ]);
    
    return response()->json($post, 201);
}
```

### Example 2: Update Post (Author vs Admin)

**Scenario 1: Author updating own post**
```php
public function update(Request $request, Post $post): JsonResponse
{
    $this->authorize('update', $post);
    
    // Policy checks:
    // 1. Is user the author? YES ✓
    // 2. Has 'update posts' permission? YES ✓
    
    $post->update($request->validated());
    return response()->json($post);
}
```

**Scenario 2: Author trying to update another's post**
```php
// Policy check fails:
// $user->id === $post->user_id → FALSE (not author)
// Even though user has permission, authorization denied
// Response: 403 Forbidden
```

**Scenario 3: Admin updating any post**
```php
public function update(Request $request, Post $post): JsonResponse
{
    $this->authorize('update', $post);
    
    // Policy checks:
    // 1. Is user admin? YES ($user->hasRole('admin')) ✓
    // 2. Has 'update posts' permission? YES ✓
    
    $post->update($request->validated());
    return response()->json($post);
}
```

### Example 3: Publishing a Post

**Only editors and admins can publish:**

```php
public function publish(Post $post): JsonResponse
{
    $this->authorize('publish', $post);
    
    // Policy checks: $user->hasPermissionTo('publish posts')
    // Only 'editor' and 'admin' roles have this permission
    
    $post->update([
        'status' => 'published',
        'published_at' => now(),
    ]);
    
    return response()->json($post);
}
```

---

## Testing Authorization

### Test If User Can Perform Action

```php
// In controller
if ($user->cannot('update', $post)) {
    abort(403, 'Unauthorized');
}

// In request
public function authorize(): bool
{
    return auth()->user()->can('create posts');
}
```

### Manual Authorization Tests

```php
// Check permission
$user->hasPermissionTo('create posts'); // true/false

// Check role
$user->hasRole('admin'); // true/false

// Check if author
$post->author_id === auth()->id(); // true/false

// Combined
if ($user->hasRole('admin') || $post->author_id === $user->id) {
    // Allow
}
```

---

## API Endpoints with Authorization

### Public Endpoints (No Auth Required)
```
POST   /api/auth/register
POST   /api/auth/login
```

### Protected Endpoints (Auth + Policy)
```
GET    /api/posts              - View any (need 'list posts')
POST   /api/posts              - Create (need 'create posts')
GET    /api/posts/{id}         - View single (need 'read posts')
PUT    /api/posts/{id}         - Update (need 'update posts' + author/admin)
DELETE /api/posts/{id}         - Delete (need 'delete posts' + author/admin)
POST   /api/posts/{id}/publish - Publish (need 'publish posts')

GET    /api/users              - List users (need 'list users')
GET    /api/users/{id}         - View user (need 'read users')
PUT    /api/users/{id}         - Update user (need 'update users' + owner/admin)
DELETE /api/users/{id}         - Delete user (need 'delete users')
```

---

## Creating New Roles/Permissions at Runtime

```php
// Create permission
$permission = Permission::create(['name' => 'moderate comments']);

// Create role
$role = Role::create(['name' => 'moderator']);

// Assign permission to role
$role->givePermissionTo($permission);

// Assign role to user
$user->assignRole($role);

// Give direct permission to user
$user->givePermissionTo($permission);
```

---

## Best Practices

1. **Use Policies** for model-based authorization
2. **Use Middleware** for route-level checks
3. **Cache Permissions** for better performance
4. **Clear Cache** when changing permissions:
   ```php
   php artisan cache:clear
   // OR in code: app()['cache']->forget('spatie.permission.cache');
   ```
5. **Test Thoroughly** - always test authorization logic
6. **Document Permissions** - keep permission requirements clear
7. **Log Authorization** - track who accessed what
8. **Role Hierarchy** - use super-admin for full access

---

## Useful Artisan Commands

```bash
# List all permissions
php artisan permission:list

# Create permission
php artisan permission:create-permission {name}

# Create role
php artisan permission:create-role {name}

# Assign permission to role
php artisan permission:assign-permission {role} {permission}

# Reseed permissions
php artisan db:seed --class=PermissionSeeder

# Clear permission cache
php artisan cache:clear
```

---

## Common Issues & Solutions

### Issue: Authorization always fails
```php
// Solution: Check if permission exists
if (!Permission::where('name', 'my-permission')->exists()) {
    Permission::create(['name' => 'my-permission']);
}

// Clear cache
app()['cache']->forget('spatie.permission.cache');
```

### Issue: Role not working
```php
// Check if role is assigned
$user->hasRole('editor'); // false?

// Solution: Verify assignment
$user->assignRole('editor');
// or
$user->syncRoles('editor', 'author');
```

---

## Summary

Your Laravel app now has:
- ✅ 5 Roles (super-admin, admin, editor, author, user)
- ✅ 23 Permissions (users, posts, comments, admin)
- ✅ PostPolicy (model-based authorization)
- ✅ CheckPermission Middleware (route-level)
- ✅ Protected API Routes
- ✅ Example Controllers with authorization

Start by testing the API endpoints to see authorization in action!
