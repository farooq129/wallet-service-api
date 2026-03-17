Wallet Service API

A robust, purely backend REST API simulating a Wallet Service. This system allows for the creation of wallets, processing of deposits and withdrawals, and atomic transfers between wallets.

Architectural Decisions

This project follows a strict Layered Architecture (Repository-Service Pattern) to ensure maintainability, scalability, and data integrity:

Controllers: Handle HTTP requests and input validation.

Services: Contain the core business logic, ensuring atomicity via database transactions and handling idempotency checks.

Repositories: Manage all database interactions, keeping queries isolated and reusable.

Data Transfer Objects (DTOs): Ensure strongly typed data is passed between layers (e.g., TransferDTO).

Core Business Rules Implemented

Monetary Precision: All balances and amounts are stored and processed as integers (minor units/cents) to prevent floating-point inaccuracies.

Atomicity & Concurrency: Database transactions (DB::transaction) and row-level locking (lockForUpdate) are utilized to prevent race conditions and ensure complete atomic transfers.

Idempotency: Deposit, withdrawal, and transfer endpoints support Idempotency-Key headers to safely handle retries without duplicating transactions.

Double-Entry Accounting: Transfers securely log both the debit (transfer_out) and credit (transfer_in) sides of the transaction.

Setup Instructions

Prerequisites

PHP 8.2+

Composer

SQLite (or MySQL)

Installation

Clone the repository

git clone <your-repository-url>
cd wallet-service-api


Install dependencies

composer install


Environment Setup

cp .env.example .env
php artisan key:generate


Note: The default database is configured to SQLite for easy setup.

Run Database Migrations

php artisan migrate


Start the Local Server

php artisan serve


The API will be accessible at http://127.0.0.1:8000.

API Endpoints Overview

A full Postman collection (Wallet_Service_API.postman_collection.json) is included in the root of this repository for easy testing.

1. Health & Wallets

GET /api/health - Check API status.

POST /api/wallets - Create a new wallet.

GET /api/wallets - List all wallets.

GET /api/wallets/{id} - Get specific wallet details.

GET /api/wallets/{id}/balance - Get wallet balance.

2. Transactions

POST /api/wallets/{id}/deposit - Add funds.

POST /api/wallets/{id}/withdraw - Remove funds.

POST /api/transfers - Move funds between wallets.

GET /api/wallets/{id}/transactions - View wallet history.

Future Improvements & Scaling Considerations

Queue System: Offload the generation of transaction history reports or email receipts to background jobs (Redis/RabbitMQ).

Database Scaling: Switch from SQLite to PostgreSQL/MySQL and implement read replicas for high-traffic GET requests (like balance checks).

Authentication: Implement Laravel Sanctum or Passport to secure the endpoints using Bearer tokens once the service is ready for production.