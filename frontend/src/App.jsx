import React from "react";
import {
  BrowserRouter,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";
import { ConfigProvider } from "antd";

import MainLayout from "./component/Layout/MainLayout";
import ProtectedRoute from "./component/ProtectedRoute";

import Login from "./pages/login";
import Dashboard from "./pages/Dashboard";
import Students from "./pages/Students";
import StudentRegistration from "./pages/StudentRegistration";
import StudentDetails from "./pages/StudentDetails";
import StudentEdit from "./pages/StudentEdit";

const PlaceholderPage = ({ title, description }) => {
  return (
    <div
      style={{
        background: "#fff",
        borderRadius: 14,
        padding: 40,
        minHeight: 400,
      }}
    >
      <h1 style={{ color: "#6e1423" }}>{title}</h1>

      <p style={{ color: "#8c8c8c" }}>
        {description}
      </p>
    </div>
  );
};

function App() {
  return (
    <ConfigProvider
      theme={{
        token: {
          colorPrimary: "#f9b233",
          borderRadius: 10,
          fontFamily:
            "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif",
        },
      }}
    >
      <BrowserRouter>
        <Routes>

          {/* =========================
              PUBLIC ROUTES
          ========================= */}

          <Route
            path="/login"
            element={<Login />}
          />

          {/* =========================
              PROTECTED APPLICATION
          ========================= */}

          <Route
            path="/"
            element={
              <ProtectedRoute>
                <MainLayout />
              </ProtectedRoute>
            }
          >
            {/* =========================
                ROOT → DASHBOARD
            ========================= */}

            <Route
              index
              element={
                <Navigate
                  to="/dashboard"
                  replace
                />
              }
            />

            {/* =========================
                DASHBOARD
                All authenticated users
            ========================= */}

            <Route
              path="dashboard"
              element={<Dashboard />}
            />

            {/* =========================
                STUDENTS
                ADMIN ONLY
            ========================= */}

            <Route
              path="students"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <Students />
                </ProtectedRoute>
              }
            />

            <Route
              path="students/register"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <StudentRegistration />
                </ProtectedRoute>
              }
            />

            <Route
              path="students/:id"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <StudentDetails />
                </ProtectedRoute>
              }
            />

            <Route
              path="students/:id/edit"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <StudentEdit />
                </ProtectedRoute>
              }
            />

            {/* =========================
                FIELD PLACEMENTS
                ADMIN ONLY FOR NOW
            ========================= */}

            <Route
              path="placements"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <PlaceholderPage
                    title="Field Placements"
                    description="Field placement management module will be available here."
                  />
                </ProtectedRoute>
              }
            />

            {/* =========================
                REPORTS
                ADMIN ONLY FOR NOW
            ========================= */}

            <Route
              path="reports"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <PlaceholderPage
                    title="Reports"
                    description="Reports module will be available here."
                  />
                </ProtectedRoute>
              }
            />

            {/* =========================
                USERS
                ADMIN ONLY
            ========================= */}

            <Route
              path="users"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <PlaceholderPage
                    title="Users"
                    description="User management module will be available here."
                  />
                </ProtectedRoute>
              }
            />

            {/* =========================
                ROLES
                ADMIN ONLY
            ========================= */}

            <Route
              path="roles"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <PlaceholderPage
                    title="Roles & Permissions"
                    description="Roles and permissions module will be available here."
                  />
                </ProtectedRoute>
              }
            />

            {/* =========================
                SETTINGS
                ADMIN ONLY
            ========================= */}

            <Route
              path="settings"
              element={
                <ProtectedRoute allowedRoles={["admin"]}>
                  <PlaceholderPage
                    title="Settings"
                    description="System settings will be available here."
                  />
                </ProtectedRoute>
              }
            />
          </Route>

          {/* =========================
              UNKNOWN ROUTES
          ========================= */}

          <Route
            path="*"
            element={
              <Navigate
                to="/dashboard"
                replace
              />
            }
          />

        </Routes>
      </BrowserRouter>
    </ConfigProvider>
  );
}

export default App;
