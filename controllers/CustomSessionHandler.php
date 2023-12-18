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
            $stmt = $this->db->prepare("SELECT data FROM sessions WHERE session_id = ?");
            $stmt->bind_param("s", $sessionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();

            // Check if 'data' key exists in the $data array
            if (isset($data['data'])) {
                return $data['data'];
            } else {
                return '';
            }
        }

        public function write($sessionId, $data): bool
        {
            // Get user_id from session data or another source
            $userId = $this->getUserIdFromSessionData($data);

            // Clear previous sessions for the same user_id
            $this->clearPreviousSessions($userId);

            $stmt = $this->db->prepare("REPLACE INTO sessions (session_id, user_id, data, timestamp) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $sessionId, $userId, $data);
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

        private function getUserIdFromSessionData($data)
        {
            $sessionData = $_SESSION;

            // Check if 'user_id' key exists in the $sessionData array
            if (isset($sessionData['user_id'])) {
                return $sessionData['user_id'];
            } else {
                return -1;  // or another default value
            }
        }
    }

    $customSessionHandler = new CustomSessionHandler();
    session_set_save_handler($customSessionHandler, true);
?>
