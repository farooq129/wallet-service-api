<h1>💼 Wallet Service API</h1>

<p>
A robust, backend-only REST API that simulates a digital wallet system.
This service enables wallet creation, deposits, withdrawals, and secure fund transfers
with strong data integrity and scalability principles.
</p>

<hr/>

<h2>🚀 Features</h2>
<ul>
  <li>Create and manage wallets</li>
  <li>Deposit and withdraw funds</li>
  <li>Atomic transfers between wallets</li>
  <li>Transaction history tracking</li>
  <li>Idempotent API operations</li>
  <li>High precision monetary handling</li>
</ul>

<hr/>

<h2>🏗️ Architecture</h2>

<p>This project follows a <strong>Layered Architecture (Repository-Service Pattern)</strong>:</p>

<ul>
  <li><strong>Controllers:</strong> Handle HTTP requests and input validation</li>
  <li><strong>Services:</strong> Contain business logic and ensure atomic operations</li>
  <li><strong>Repositories:</strong> Manage database interactions</li>
  <li><strong>DTOs:</strong> Ensure structured data transfer (e.g., TransferDTO)</li>
</ul>

<hr/>

<h2>⚙️ Core Business Rules</h2>

<h3>💰 Monetary Precision</h3>
<p>
All balances and amounts are stored as integers (minor units/cents) to prevent floating-point inaccuracies.
</p>

<h3>🔒 Atomicity & Concurrency</h3>
<p>
Uses database transactions (<code>DB::transaction</code>) and row-level locking (<code>lockForUpdate</code>)
to prevent race conditions and ensure safe transfers.
</p>

<h3>🔁 Idempotency</h3>
<p>
Supports <code>Idempotency-Key</code> headers to safely retry requests without duplicating transactions.
</p>

<h3>📊 Double-Entry Accounting</h3>
<p>
Each transfer logs both:
</p>
<ul>
  <li><code>transfer_out</code> (debit)</li>
  <li><code>transfer_in</code> (credit)</li>
</ul>

<hr/>

<h2>🛠️ Setup Instructions</h2>

<h3>✅ Prerequisites</h3>
<ul>
  <li>PHP 8.2+</li>
  <li>Composer</li>
  <li>SQLite or MySQL</li>
</ul>

<h3>📦 Installation</h3>

<pre><code>git clone &lt;your-repository-url&gt;
cd wallet-service-api
composer install
</code></pre>

<h3>⚙️ Environment Setup</h3>

<pre><code>cp .env.example .env
php artisan key:generate
</code></pre>

<p><strong>Note:</strong> Default database is configured for SQLite.</p>

<h3>🗄️ Run Migrations</h3>

<pre><code>php artisan migrate
</code></pre>

<h3>▶️ Start Server</h3>

<pre><code>php artisan serve
</code></pre>

<p>API will be available at:</p>

<pre><code>http://127.0.0.1:8000</code></pre>

<hr/>

<h2>📡 API Endpoints</h2>

<p>A full Postman collection is included: <code>Wallet_Service_API.postman_collection.json</code></p>

<h3>🔹 Health & Wallets</h3>
<ul>
  <li><strong>GET</strong> /api/health - Check API status</li>
  <li><strong>POST</strong> /api/wallets - Create wallet</li>
  <li><strong>GET</strong> /api/wallets - List wallets</li>
  <li><strong>GET</strong> /api/wallets/{id} - Wallet details</li>
  <li><strong>GET</strong> /api/wallets/{id}/balance - Wallet balance</li>
</ul>

<h3>🔹 Transactions</h3>
<ul>
  <li><strong>POST</strong> /api/wallets/{id}/deposit - Deposit funds</li>
  <li><strong>POST</strong> /api/wallets/{id}/withdraw - Withdraw funds</li>
  <li><strong>POST</strong> /api/transfers - Transfer funds</li>
  <li><strong>GET</strong> /api/wallets/{id}/transactions - Transaction history</li>
</ul>

<hr/>


