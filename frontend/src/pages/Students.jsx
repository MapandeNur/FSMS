import React, { useEffect, useMemo, useState } from "react";
import {
  Button,
  Card,
  Col,
  Input,
  Row,
  Select,
  Space,
  Table,
  Tag,
  Typography,
  message,
} from "antd";
import {
  PlusOutlined,
  SearchOutlined,
  EyeOutlined,
  ReloadOutlined,
} from "@ant-design/icons";
import { useNavigate } from "react-router-dom";

import api from "../api/axios";

const { Title, Text } = Typography;

const Students = () => {
  const navigate = useNavigate();

  const [students, setStudents] = useState([]);
  const [loading, setLoading] = useState(false);

  const [searchText, setSearchText] = useState("");
  const [genderFilter, setGenderFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState("all");
  const [institutionFilter, setInstitutionFilter] = useState("all");

  // =========================
  // FETCH STUDENTS FROM API
  // =========================
  const fetchStudents = async () => {
    setLoading(true);

    try {
      const response = await api.get("/v1/students");

      const data = response.data?.data || [];

      setStudents(Array.isArray(data) ? data : []);
    } catch (error) {
      console.error("Failed to fetch students:", error);

      message.error(
        error.response?.data?.message ||
          "Failed to load students."
      );

      setStudents([]);
    } finally {
      setLoading(false);
    }
  };

  // Load students when page opens
  useEffect(() => {
    fetchStudents();
  }, []);

  // =========================
  // PREPARE STUDENT DATA
  // =========================
  const formattedStudents = useMemo(() => {
    return students.map((student) => ({
      ...student,

      key: student.id,

      name: [
        student.first_name,
        student.middle_name,
        student.last_name,
      ]
        .filter(Boolean)
        .join(" "),

      institution: student.institution_name,

      programme: student.programme_of_study,

      year: `Year ${student.year_of_study}`,

      displayStatus:
        student.status?.charAt(0).toUpperCase() +
          student.status?.slice(1) || "Unknown",
    }));
  }, [students]);

  // =========================
  // GET UNIQUE INSTITUTIONS
  // =========================
  const institutions = useMemo(() => {
    return [
      ...new Set(
        formattedStudents
          .map((student) => student.institution)
          .filter(Boolean)
      ),
    ];
  }, [formattedStudents]);

  // =========================
  // FILTER STUDENTS
  // =========================
  const filteredStudents = useMemo(() => {
    return formattedStudents.filter((student) => {
      const search = searchText
        .toLowerCase()
        .trim();

      const name =
        student.name?.toLowerCase() || "";

      const registrationNumber =
        student.registration_number
          ?.toLowerCase() || "";

      const institution =
        student.institution
          ?.toLowerCase() || "";

      const matchesSearch =
        name.includes(search) ||
        registrationNumber.includes(search) ||
        institution.includes(search);

      const matchesGender =
        genderFilter === "all" ||
        student.gender?.toLowerCase() ===
          genderFilter.toLowerCase();

      const matchesStatus =
        statusFilter === "all" ||
        student.status?.toLowerCase() ===
          statusFilter.toLowerCase();

      const matchesInstitution =
        institutionFilter === "all" ||
        student.institution === institutionFilter;

      return (
        matchesSearch &&
        matchesGender &&
        matchesStatus &&
        matchesInstitution
      );
    });
  }, [
    formattedStudents,
    searchText,
    genderFilter,
    statusFilter,
    institutionFilter,
  ]);

  // =========================
  // RESET FILTERS
  // =========================
  const handleReset = () => {
    setSearchText("");
    setGenderFilter("all");
    setStatusFilter("all");
    setInstitutionFilter("all");
  };

  // =========================
  // STATUS TAG
  // =========================
  const renderStatus = (status) => {
    const normalizedStatus =
      status?.toLowerCase();

    let color = "default";

    if (normalizedStatus === "active") {
      color = "green";
    } else if (normalizedStatus === "pending") {
      color = "orange";
    } else if (normalizedStatus === "completed") {
      color = "blue";
    } else if (normalizedStatus === "inactive") {
      color = "red";
    }

    return (
      <Tag color={color}>
        {status || "Unknown"}
      </Tag>
    );
  };

  // =========================
  // TABLE COLUMNS
  // =========================
  const columns = [
    {
      title: "Registration No.",
      dataIndex: "registration_number",
      key: "registration_number",
      render: (value) => (
        <Text
          strong
          style={{
            color: "#6e1423",
          }}
        >
          {value}
        </Text>
      ),
    },

    {
      title: "Student Name",
      dataIndex: "name",
      key: "name",
    },

    {
      title: "Gender",
      dataIndex: "gender",
      key: "gender",
    },

    {
      title: "Institution",
      dataIndex: "institution",
      key: "institution",
    },

    {
      title: "Programme",
      dataIndex: "programme",
      key: "programme",
    },

    {
      title: "Year",
      dataIndex: "year",
      key: "year",
    },

    {
      title: "Status",
      dataIndex: "displayStatus",
      key: "status",
      render: (status) =>
        renderStatus(status),
    },

    {
      title: "Action",
      key: "action",
      fixed: "right",
      render: (_, record) => (
        <Button
          type="link"
          icon={<EyeOutlined />}
          onClick={() =>
            navigate(`/students/${record.id}`)
          }
        >
          View
        </Button>
      ),
    },
  ];

  return (
    <div>
      {/* =========================
          PAGE HEADER
      ========================= */}
      <Row
        justify="space-between"
        align="middle"
        style={{
          marginBottom: 24,
        }}
      >
        <Col>
          <Title
            level={2}
            style={{
              marginBottom: 4,
              color: "#6e1423",
            }}
          >
            Students
          </Title>

          <Text type="secondary">
            Manage registered field students
          </Text>
        </Col>

        <Col>
          <Space>
            <Button
              icon={<ReloadOutlined />}
              onClick={fetchStudents}
              loading={loading}
            >
              Refresh
            </Button>

            <Button
              type="primary"
              icon={<PlusOutlined />}
              onClick={() =>
                navigate("/students/register")
              }
              style={{
                background: "#6e1423",
                borderColor: "#6e1423",
                height: 42,
                borderRadius: 8,
              }}
            >
              Register Student
            </Button>
          </Space>
        </Col>
      </Row>

      {/* =========================
          SEARCH & FILTERS
      ========================= */}
      <Card
        bordered={false}
        style={{
          marginBottom: 20,
          borderRadius: 14,
        }}
      >
        <Row gutter={[16, 16]}>
          {/* SEARCH */}
          <Col xs={24} lg={8}>
            <Input
              size="large"
              prefix={<SearchOutlined />}
              placeholder="Search name, registration number..."
              value={searchText}
              onChange={(e) =>
                setSearchText(e.target.value)
              }
              allowClear
            />
          </Col>

          {/* GENDER */}
          <Col xs={24} sm={12} lg={4}>
            <Select
              size="large"
              style={{
                width: "100%",
              }}
              value={genderFilter}
              onChange={setGenderFilter}
              options={[
                {
                  value: "all",
                  label: "All Genders",
                },
                {
                  value: "male",
                  label: "Male",
                },
                {
                  value: "female",
                  label: "Female",
                },
              ]}
            />
          </Col>

          {/* STATUS */}
          <Col xs={24} sm={12} lg={4}>
            <Select
              size="large"
              style={{
                width: "100%",
              }}
              value={statusFilter}
              onChange={setStatusFilter}
              options={[
                {
                  value: "all",
                  label: "All Status",
                },
                {
                  value: "active",
                  label: "Active",
                },
                {
                  value: "pending",
                  label: "Pending",
                },
                {
                  value: "completed",
                  label: "Completed",
                },
                {
                  value: "inactive",
                  label: "Inactive",
                },
              ]}
            />
          </Col>

          {/* INSTITUTION */}
          <Col xs={24} sm={12} lg={5}>
            <Select
              size="large"
              style={{
                width: "100%",
              }}
              value={institutionFilter}
              onChange={setInstitutionFilter}
              placeholder="Institution"
              options={[
                {
                  value: "all",
                  label: "All Institutions",
                },

                ...institutions.map(
                  (institution) => ({
                    value: institution,
                    label: institution,
                  })
                ),
              ]}
            />
          </Col>

          {/* RESET */}
          <Col xs={24} sm={12} lg={3}>
            <Button
              size="large"
              icon={<ReloadOutlined />}
              onClick={handleReset}
              style={{
                width: "100%",
              }}
            >
              Reset
            </Button>
          </Col>
        </Row>
      </Card>

      {/* =========================
          RESULT COUNT
      ========================= */}
      <div
        style={{
          marginBottom: 12,
        }}
      >
        <Text type="secondary">
          Showing{" "}
          <strong>
            {filteredStudents.length}
          </strong>{" "}
          student
          {filteredStudents.length !== 1
            ? "s"
            : ""}
        </Text>
      </div>

      {/* =========================
          STUDENTS TABLE
      ========================= */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
        }}
      >
        <Table
          columns={columns}
          dataSource={filteredStudents}
          loading={loading}
          rowKey="id"
          scroll={{ x: 1100 }}
          pagination={{
            pageSize: 10,
            showSizeChanger: true,
            showTotal: (total, range) =>
              `${range[0]}-${range[1]} of ${total} students`,
          }}
          locale={{
            emptyText:
              loading
                ? "Loading students..."
                : "No students found",
          }}
        />
      </Card>
    </div>
  );
};

export default Students;