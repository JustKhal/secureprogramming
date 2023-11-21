<?php
    class CustomSessionHandler implements SessionHandlerInterface
    {
        private $db;

        public function open($savePath, $sessionName): bool
        {
            $this->db = new mysqli('127.0.0.1', 'root', '', 'sp');

            if ($this->db->connect_error) {
                return false;
            }

            return true;
        }

        public function close(): bool
        {
            $this->db->close();
            return true;
        }

        public function read($sessionId): string|false
        {
            $stmt = $this->db->prepare("SELECT user_id FROM sessions WHERE session_id = ?");
            $stmt->bind_param("s", $sessionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();

            return $data['user_id'] ?: '';
        }

        public function write($sessionId, $data): bool
        {
            $userId = $data;

            // Clear previous sessions for the same user_id
            $this->clearPreviousSessions($userId);

            $stmt = $this->db->prepare("REPLACE INTO sessions (session_id, user_id, timestamp) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $sessionId, $userId);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }

        public function destroy($sessionId): bool
        {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE session_id = ?");
            $stmt->bind_param("s", $sessionId);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }

        public function gc($maxLifetime): int|false
        {
            $oldSessions = time() - $maxLifetime;

            $stmt = $this->db->prepare("DELETE FROM sessions WHERE timestamp < ?");
            $stmt->bind_param("i", $oldSessions);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }

        private function clearPreviousSessions($userId)
        {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE user_id = ?");
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $stmt->close();
        }
    }

    $customSessionHandler = new CustomSessionHandler();
    session_set_save_handler($customSessionHandler, true);
