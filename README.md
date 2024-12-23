# Rico360 Voice Call System

A real-time voice calling application built with Laravel, Twilio, and WebRTC. This system enables browser-to-browser voice calls with features like call history, user status tracking, and call management.

## Features

- Real-time browser-to-browser voice calls
- User presence detection (online, offline, in call)
- Call history with filtering and sorting
- Call controls (mute, hangup)
- Incoming call notifications
- Dark/Light mode support

## Prerequisites

- PHP >= 8.1
- Laravel 10.x
- Node.js and NPM
- MySQL/PostgreSQL
- Twilio Account (with Voice API enabled)
- SSL Certificate (required for WebRTC) or Ngrok for local development
- Composer

## Step-by-Step Installation

### Clone and Basic Setup
    git clone https://github.com/lfqqueiroz/Rico360.git

### Clone the repository
    -git clone https://github.com/lfqqueiroz/Rico360.git
    
### Navigate to project folder
    cd Rico360
    
### Install PHP dependencies
    composer install
    
### Install JavaScript dependencies
    npm install
    
### Copy environment file
    cp .env.example .env
    
##  2. Database Setup

### Create database
    mysql -u root -p
    CREATE DATABASE rico360;
    exit;

### Run migrations
    php artisan migrate


## 3. Environment Configuration 

### Edit your `.env` file with the following:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=rico360
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    TWILIO_ACCOUNT_SID=your_twilio_account_sid
    TWILIO_AUTH_TOKEN=your_twilio_auth_token
    TWILIO_APP_SID=your_twilio_app_sid
    PUSHER_APP_ID=your_pusher_app_id
    PUSHER_APP_KEY=your_pusher_app_key
    PUSHER_APP_SECRET=your_pusher_app_secret
    PUSHER_HOST=
    PUSHER_PORT=443
    PUSHER_SCHEME=https
    BROADCAST_DRIVER=pusher
    
## 4. Application Setup

### Generate application key
    php artisan key:generate

### Build assets
    npm run dev

## 5. Ngrok Setup (For Local Development)

### 1. Install Ngrok:
    npm install ngrok -g
    Or download from ngrok.com
    
### 2. Start Laravel Development Server:
    php artisan serve

### 3. Start Ngrok in a new terminal:
    ngrok http 8000
    
### 4. Copy the HTTPS URL provided by Ngrok (e.g., `https://your-ngrok-url.ngrok.io`)

### 5. Update your `.env` file:
    APP_URL=https://your-ngrok-url.ngrok.io


## 6. Twilio Configuration

### 1. Create a Twilio account at [twilio.com](https://www.twilio.com)
### 2. Get your Account SID and Auth Token from the Twilio Console
### 3. Create a new TwiML App in the Twilio Console:
       - Go to Voice > TwiML Apps > Create new TwiML App
       - Name it "Rico360"
       - Set Voice REQUEST URL to: `https://your-ngrok-url.ngrok.io/api/twiml-response`
       - Method: POST
       - Save the TwiML App SID

### 4. Update your `.env` file with Twilio credentials:
    TWILIO_ACCOUNT_SID=your_account_sid
    TWILIO_AUTH_TOKEN=your_auth_token
    TWILIO_APP_SID=your_twiml_app_sid

## Running the Application

### 1. Start the Laravel server:
    php artisan serve
### 2. In a new terminal, start Vite for asset compilation:
    npm run dev
### 3. In another terminal, start Ngrok:
    ngrok http 8000

### 4. Access the application:
       - Open the Ngrok HTTPS URL in your browser
       - Register a new account
       - Grant microphone permissions when prompted

## Usage

    1. Register at least two user accounts (use different browsers or incognito mode)
    2. Log in with both accounts
    3. Users will appear in each other's sidebar when online
    4. Click on a user's name to initiate a call
    5. Accept/Reject incoming calls using the control buttons
    6. Use mute/hangup buttons during active calls
    7. View call history in the dashboard

## Troubleshooting

## Common Issues:

### 1. Calls not connecting:
       - Verify Ngrok is running and URL is updated in Twilio
       - Check browser console for errors
       - Ensure microphone permissions are granted

### 2. Real-time updates not working:
       - Verify Pusher credentials
       - Check browser console for WebSocket errors
       - Ensure `.env` has correct BROADCAST_DRIVER

### 3. Database issues:
       - Check database credentials in `.env`
       - Ensure migrations are run
       - Verify database server is running

## Ngrok Tips:
    - URL changes on every restart (free plan)
    - Use `ngrok http --host-header=rewrite 8000` if needed
    - Access Ngrok inspector at `http://127.0.0.1:4040`
    - Update Twilio and `.env` when URL changes

## Security Notes

    - Never commit `.env` file
    - Keep Twilio credentials secure
    - Use HTTPS in production
    - Implement proper user authentication
    - Regular security updates

## Support

    For support:
    1. Check the troubleshooting section
    2. Review Twilio documentation
    3. Create an issue in the GitHub repository
    4. Contact the development team

## License

    This project is licensed under the MIT License.    

## Credits

    - Laravel Framework
    - Twilio SDK
    - WebRTC
    - Tailwind CSS
    - Ngrok
