import {
  Button,
  Card,
  Col,
  DatePicker,
  Form,
  Input,
  Row,
  Select,
  Space,
  Typography,
  message,
} from "antd";
import {
  ArrowLeftOutlined,
  SaveOutlined,
} from "@ant-design/icons";
import { useNavigate } from "react-router-dom";

const { Title, Text } = Typography;

const StudentRegistration = () => {
  const navigate = useNavigate();
  const [form] = Form.useForm();

  const handleSubmit = (values) => {
    console.log("Student registration data:", values);

    message.success("Student registered successfully!");

    form.resetFields();
    navigate("/students");
  };

  return (
    <div>
      {/* HEADER */}
      <div
        style={{
          display: "flex",
          alignItems: "center",
          marginBottom: 24,
        }}
      >
        <Button
          type="text"
          icon={<ArrowLeftOutlined />}
          onClick={() => navigate("/students")}
          style={{
            color: "#6e1423",
            marginRight: 10,
          }}
        />

        <div>
          <Title
            level={2}
            style={{
              margin: 0,
              color: "#6e1423",
            }}
          >
            Register Student
          </Title>

          <Text type="secondary">
            Add a new field student to the system
          </Text>
        </div>
      </div>

      {/* FORM */}
      <Card
        bordered={false}
        style={{
          borderRadius: 14,
        }}
      >
        <Form
          form={form}
          layout="vertical"
          onFinish={handleSubmit}
          autoComplete="off"
        >
          {/* PERSONAL INFORMATION */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginBottom: 20,
            }}
          >
            Personal Information
          </Title>

          <Row gutter={[20, 0]}>
            <Col xs={24} md={8}>
              <Form.Item
                label="First Name"
                name="first_name"
                rules={[
                  {
                    required: true,
                    message: "Please enter first name",
                  },
                  {
                    min: 2,
                    message: "First name must be at least 2 characters",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="Enter first name"
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Middle Name"
                name="middle_name"
              >
                <Input
                  size="large"
                  placeholder="Enter middle name"
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Last Name"
                name="last_name"
                rules={[
                  {
                    required: true,
                    message: "Please enter last name",
                  },
                  {
                    min: 2,
                    message: "Last name must be at least 2 characters",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="Enter last name"
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Gender"
                name="gender"
                rules={[
                  {
                    required: true,
                    message: "Please select gender",
                  },
                ]}
              >
                <Select
                  size="large"
                  placeholder="Select gender"
                  options={[
                    {
                      value: "Male",
                      label: "Male",
                    },
                    {
                      value: "Female",
                      label: "Female",
                    },
                  ]}
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Date of Birth"
                name="date_of_birth"
                rules={[
                  {
                    required: true,
                    message: "Please select date of birth",
                  },
                ]}
              >
                <DatePicker
                  size="large"
                  style={{ width: "100%" }}
                  placeholder="Select date of birth"
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Registration Number"
                name="registration_number"
                rules={[
                  {
                    required: true,
                    message: "Please enter registration number",
                  },
                  {
                    pattern: /^FS\/\d{4}\/\d{4}$/,
                    message:
                      "Format must be FS/YYYY/0001",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="FS/2026/0001"
                />
              </Form.Item>
            </Col>
          </Row>

          {/* CONTACT INFORMATION */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginTop: 25,
              marginBottom: 20,
            }}
          >
            Contact Information
          </Title>

          <Row gutter={[20, 0]}>
            <Col xs={24} md={12}>
              <Form.Item
                label="Email"
                name="email"
                rules={[
                  {
                    required: true,
                    message: "Please enter email",
                  },
                  {
                    type: "email",
                    message: "Please enter a valid email",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="student@example.com"
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={12}>
              <Form.Item
                label="Phone"
                name="phone"
                rules={[
                  {
                    required: true,
                    message: "Please enter phone number",
                  },
                  {
                    pattern: /^(?:\+255|0)[67]\d{8}$/,
                    message:
                      "Enter a valid Tanzania phone number",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="0712345678"
                />
              </Form.Item>
            </Col>
          </Row>

          {/* ACADEMIC INFORMATION */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginTop: 25,
              marginBottom: 20,
            }}
          >
            Academic Information
          </Title>

          <Row gutter={[20, 0]}>
            <Col xs={24} md={8}>
              <Form.Item
                label="Institution"
                name="institution"
                rules={[
                  {
                    required: true,
                    message: "Please enter institution",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="Enter institution"
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Programme"
                name="programme"
                rules={[
                  {
                    required: true,
                    message: "Please enter programme",
                  },
                ]}
              >
                <Input
                  size="large"
                  placeholder="e.g. Information Technology"
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Year of Study"
                name="year_of_study"
                rules={[
                  {
                    required: true,
                    message: "Please select year of study",
                  },
                ]}
              >
                <Select
                  size="large"
                  placeholder="Select year"
                  options={[
                    {
                      value: "Year 1",
                      label: "Year 1",
                    },
                    {
                      value: "Year 2",
                      label: "Year 2",
                    },
                    {
                      value: "Year 3",
                      label: "Year 3",
                    },
                    {
                      value: "Year 4",
                      label: "Year 4",
                    },
                    {
                      value: "Year 5",
                      label: "Year 5",
                    },
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>

          {/* FIELD PLACEMENT */}
          <Title
            level={4}
            style={{
              color: "#6e1423",
              marginTop: 25,
              marginBottom: 20,
            }}
          >
            Field Placement
          </Title>

          <Row gutter={[20, 0]}>
            <Col xs={24} md={8}>
              <Form.Item
                label="Start Date"
                name="start_date"
                rules={[
                  {
                    required: true,
                    message: "Please select start date",
                  },
                ]}
              >
                <DatePicker
                  size="large"
                  style={{ width: "100%" }}
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="End Date"
                name="end_date"
                rules={[
                  {
                    required: true,
                    message: "Please select end date",
                  },
                ]}
              >
                <DatePicker
                  size="large"
                  style={{ width: "100%" }}
                />
              </Form.Item>
            </Col>

            <Col xs={24} md={8}>
              <Form.Item
                label="Status"
                name="status"
                initialValue="Pending"
                rules={[
                  {
                    required: true,
                    message: "Please select status",
                  },
                ]}
              >
                <Select
                  size="large"
                  options={[
                    {
                      value: "Pending",
                      label: "Pending",
                    },
                    {
                      value: "Active",
                      label: "Active",
                    },
                    {
                      value: "Completed",
                      label: "Completed",
                    },
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>

          {/* ACTIONS */}
          <Form.Item style={{ marginTop: 20, marginBottom: 0 }}>
            <Space>
              <Button
                size="large"
                onClick={() => navigate("/students")}
              >
                Cancel
              </Button>

              <Button
                type="primary"
                htmlType="submit"
                icon={<SaveOutlined />}
                size="large"
                style={{
                  background: "#6e1423",
                  borderColor: "#6e1423",
                }}
              >
                Register Student
              </Button>
            </Space>
          </Form.Item>
        </Form>
      </Card>
    </div>
  );
};

export default StudentRegistration;