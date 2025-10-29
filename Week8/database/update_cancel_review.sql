-- Week 8: Add Cancellation Review Tracking
-- Add fields to track who reviewed the cancellation request and when

ALTER TABLE appointments 
ADD COLUMN cancel_reviewed_by INT(11) NULL AFTER cancel_reason,
ADD COLUMN cancel_reviewed_at DATETIME NULL AFTER cancel_reviewed_by,
ADD COLUMN cancel_review_status ENUM('approved', 'rejected') NULL AFTER cancel_reviewed_at;

-- Add foreign key for cancel_reviewed_by
ALTER TABLE appointments 
ADD FOREIGN KEY (cancel_reviewed_by) REFERENCES users(id);

-- Add index for better query performance
ALTER TABLE appointments 
ADD INDEX idx_cancel_review_status (cancel_review_status);
