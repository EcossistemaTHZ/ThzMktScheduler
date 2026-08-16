CREATE TABLE IF NOT EXISTS campaigns (
    id SERIAL PRIMARY KEY,
    subject TEXT NOT NULL,
    message TEXT NOT NULL,
    scheduled_at TIMESTAMPTZ NOT NULL,
    status TEXT DEFAULT 'pending',
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);