# Poll Management System

A modern, real-time poll management system built with Laravel 12, featuring WebSocket-powered live vote monitoring, comprehensive admin dashboard, and responsive user interface.

## 📋 Brief Description

The Poll Management System is a full-featured polling application that allows users to create, manage, and participate in polls with real-time vote tracking. Built with Laravel 12 and modern web technologies, it provides:

- **Real-time Vote Monitoring**: Live WebSocket updates for instant vote tracking
- **Admin Dashboard**: Comprehensive poll management and analytics
- **Public Poll Interface**: User-friendly voting experience
- **Guest Voting Support**: Allow anonymous voting with IP tracking
- **Responsive Design**: Works seamlessly on all devices
- **RESTful API**: Complete API for frontend and mobile integration

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Redis (optional, for caching)

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd poptin
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` file with your database and broadcasting settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=poll_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Broadcasting Configuration (for real-time features)
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

# Reverb WebSocket Configuration
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_APP_ID=your-app-id
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Step 4: Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### Step 5: Install Laravel Reverb (WebSocket Server)
```bash
php artisan reverb:install
```

### Step 6: Run Tests
```bash
php artisan test
```

### Step 7: Start Services
```bash
# Terminal 1: Start Laravel development server
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2: Start Reverb WebSocket server
php artisan reverb:start

# Terminal 3: Start queue worker (for background jobs)
php artisan queue:work
```

## 🧪 Testing

The application includes comprehensive test coverage for all major features:

### Test Structure
```
tests/
├── Feature/
│   ├── Admin/
│   │   └── PollControllerTest.php         # Admin poll management tests
│   ├── AuthControllerTest.php             # Authentication tests
│   ├── PublicPollControllerTest.php       # Public poll interface tests
│   └── VoteCastEventTest.php              # Real-time event tests
└── Unit/
    └── ExampleTest.php                     # Unit tests
```

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AuthControllerTest.php

# Run tests with coverage (if Xdebug is installed)
php artisan test --coverage
```

### Test Coverage
- ✅ **Authentication**: Login, logout, admin middleware
- ✅ **Admin Features**: Poll CRUD operations, status management
- ✅ **Public Features**: Poll listing, voting, guest voting
- ✅ **Real-time Events**: WebSocket event broadcasting
- ✅ **API Endpoints**: All RESTful API endpoints
- ✅ **Custom Exceptions**: Error handling and validation

## 🌐 Application URLs

### Public URLs
- **Main Application**: `http://127.0.0.1:8000`
- **Public Polls**: `http://127.0.0.1:8000/polls`
- **Individual Poll**: `http://127.0.0.1:8000/polls/{id}`

### Admin URLs
- **Admin Login**: `http://127.0.0.1:8000/admin/login`
- **Admin Dashboard**: `http://127.0.0.1:8000/admin/dashboard`
- **Poll Management**: `http://127.0.0.1:8000/admin/polls`
- **Live Votes Panel**: `http://127.0.0.1:8000/admin/live-votes`
- **Admin Settings**: `http://127.0.0.1:8000/admin/settings`

### API Endpoints
- **Base API URL**: `http://127.0.0.1:8000/api`
- **Admin API**: `http://127.0.0.1:8000/api/admin`
- **Public API**: `http://127.0.0.1:8000/api/polls`

## 🏗️ Project Code Structure

The application follows a clean, layered architecture pattern:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php    # Admin dashboard views
│   │   │   └── PollController.php         # Admin poll management
│   │   ├── AuthController.php             # Authentication
│   │   └── PublicPollController.php       # Public poll interface
│   ├── Middleware/
│   │   └── AdminMiddleware.php            # Admin authorization
│   └── Requests/
│       ├── Admin/
│       │   └── CreatePollRequest.php      # Admin form validation
│       └── VoteRequest.php                # Vote validation
├── Services/
│   ├── PollService.php                    # Poll business logic
│   └── VoteService.php                    # Vote business logic
├── Repositories/
│   ├── BaseRepository.php                 # Common CRUD operations
│   ├── PollRepository.php                 # Poll data access
│   ├── PollOptionRepository.php           # Option data access
│   └── VoteRepository.php                 # Vote data access
├── Models/
│   ├── Poll.php                           # Poll model
│   ├── PollOption.php                     # Poll option model
│   ├── Vote.php                           # Vote model
│   └── User.php                           # User model
├── Events/
│   └── VoteCast.php                       # Real-time vote events
├── Jobs/
│   └── BroadcastVoteEvent.php             # Background vote broadcasting
└── Exceptions/
    ├── PollNotFoundException.php          # Custom exceptions
    ├── AlreadyVotedException.php
    ├── PollOptionNotFoundException.php
    └── GuestVotingNotAllowedException.php
```

## 🔄 Architecture Pattern: Controller → Service → Repository → Model

### 1. **Controllers** (HTTP Layer)
- Handle HTTP requests and responses
- Input validation and sanitization
- Route requests to appropriate services
- Return formatted API responses

**Example:**
```php
// PollController.php
public function store(CreatePollRequest $request)
{
    $validated = $request->validated();
    $poll = $this->pollService->createPoll($pollData, $options, $adminId);
    return ApiResponse::response($poll, 'Poll created successfully', 201, true);
}
```

### 2. **Services** (Business Logic Layer)
- Contains all business logic and rules
- Orchestrates multiple repositories
- Handles complex operations and validations
- Manages transactions and error handling

**Example:**
```php
// PollService.php
public function createPoll(array $pollData, array $options, int $adminId): Poll
{
    $pollData['created_by'] = $adminId;
    $pollData['status'] = Poll::STATUS_INACTIVE;
    return $this->pollRepository->createPollWithOptions($pollData, $options);
}
```

### 3. **Repositories** (Data Access Layer)
- Abstract database operations
- Handle complex queries and relationships
- Provide clean data access interface
- Implement caching and optimization

**Example:**
```php
// PollRepository.php
public function getPollsWithVoteSummary(array $filters, int $perPage = 10): LengthAwarePaginator
{
    return $this->model
        ->with(['options' => function ($query) {
            $query->withCount('votes');
        }])
        ->withCount('votes as total_votes')
        ->where($filters)
        ->paginate($perPage);
}
```

### 4. **Models** (Data Layer)
- Define database relationships
- Handle data casting and attributes
- Implement model-specific logic
- Define validation rules

**Example:**
```php
// Poll.php
public function options(): HasMany
{
    return $this->hasMany(PollOption::class)->orderBy('order_index');
}

public function getTotalVotesAttribute(): int
{
    return $this->votes()->count();
}
```

## 🚨 Custom Exceptions

The application uses custom exceptions for better error handling:

### Exception Classes
- **`PollNotFoundException`**: When poll doesn't exist or is inactive
- **`AlreadyVotedException`**: When user/IP has already voted
- **`PollOptionNotFoundException`**: When poll option doesn't exist
- **`GuestVotingNotAllowedException`**: When guest voting is disabled

### Usage Example
```php
// In Service Layer
if (!$poll->isActive()) {
    throw new PollNotFoundException('Poll is not available');
}

if ($this->voteRepository->hasUserVoted($pollId, $userId)) {
    throw new AlreadyVotedException('You have already voted on this poll');
}
```

## 🔌 API Structure

### Admin API Endpoints
```
POST   /api/admin/polls                    # Create new poll
GET    /api/admin/polls                    # List polls with filters
GET    /api/admin/polls/vote-summary       # Polls with vote data
GET    /api/admin/polls/{id}               # Get poll details
PATCH  /api/admin/polls/{id}/status        # Update poll status
GET    /api/admin/dashboard                # Dashboard statistics
```

### Public API Endpoints
```
GET    /api/polls                          # List active polls
GET    /api/polls/{id}                     # Get poll details
POST   /api/polls/{poll}/vote              # Submit vote
GET    /api/polls/{id}/results             # Get poll results
```

### Authentication
- **Admin API**: Uses Laravel Sanctum with `auth:sanctum` middleware
- **Public API**: No authentication required (except for voting)
- **Guest Voting**: IP-based tracking for anonymous users

## 🎨 Frontend Architecture

### Technology Stack
- **Backend**: Laravel 12 with Blade templates
- **Frontend**: Vanilla JavaScript with AJAX
- **Real-time**: Laravel Reverb (WebSocket server)
- **Styling**: Custom CSS with CSS variables
- **Icons**: Font Awesome

### View Structure (Blade Templates)
```
resources/views/
├── admin/
│   ├── layouts/
│   │   └── app.blade.php                  # Admin layout template
│   ├── dashboard.blade.php                # Admin dashboard
│   ├── polls.blade.php                    # Poll management
│   ├── live-votes.blade.php               # Real-time vote panel
│   └── settings.blade.php                 # Admin settings
├── components/
│   ├── create-poll-modal.blade.php        # Poll creation modal
│   └── poll-details-modal.blade.php       # Poll details modal
└── public/
    ├── polls.blade.php                    # Public poll listing
    └── poll-detail.blade.php              # Individual poll view
```

### JavaScript Integration
- **AJAX Calls**: Fetch API for backend communication
- **WebSocket**: Pusher client for real-time updates
- **Event Handling**: Custom event system for UI updates
- **Error Handling**: Comprehensive error handling and user feedback

### Real-time Features
- **Live Vote Updates**: WebSocket-powered real-time vote tracking
- **Connection Status**: Animated connection indicators
- **Instant Notifications**: Toast notifications for new votes
- **Auto-reconnection**: Automatic WebSocket reconnection

## 🔧 Key Features

### Admin Features
- ✅ **Poll Management**: Create, edit, and delete polls
- ✅ **Real-time Monitoring**: Live vote tracking with WebSocket
- ✅ **Vote Analytics**: Comprehensive vote statistics
- ✅ **User Management**: Admin user authentication
- ✅ **Bulk Operations**: Mass poll operations

### Public Features
- ✅ **Vote Participation**: Easy voting interface
- ✅ **Guest Voting**: Anonymous voting with IP tracking
- ✅ **Real-time Results**: Live result updates
- ✅ **Mobile Responsive**: Works on all devices
- ✅ **Accessibility**: WCAG compliant design

### Technical Features
- ✅ **RESTful API**: Complete API for integration
- ✅ **WebSocket Support**: Real-time communication
- ✅ **Queue System**: Background job processing
- ✅ **Error Handling**: Comprehensive error management
- ✅ **Security**: CSRF protection, input validation
- ✅ **Performance**: Optimized queries, caching support

## 🚀 Getting Started

1. **Follow Installation Steps** above
2. **Create Admin User** using database seeder
3. **Access Admin Panel** at `/admin/login`
4. **Create Your First Poll** using the admin interface
5. **Test Real-time Features** by casting votes on public polls
6. **Monitor Live Votes** in the admin live-votes panel
7. **Run Tests** to verify everything is working correctly: `php artisan test`

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📞 Support

For support and questions, please contact the development team or create an issue in the repository.
