import { useState } from "react";
import { Form, Input, Button, Card, Typography, message}from 'antd';
import { useNavigate} from 'react-route-dom';

const {Title}=Typography;
export default function login(){
    const[loading,setLoading]=useState(false);
    const{login}=useAuth();
    const navigate = useNavigate();

    const onFinish = async (values)=> {
        setLoading(true);
        try {
            await login(values.email, values.password);
            message.success('Login succesful');
            navigate('/dashboard');

        }catch (err) {
            message.error('Invalid email or password');
        }finally{
            setLoading(false);
        }
    };
    return (
        <div style={{
            display:'flex',
            justifyContent:'center',
            alignItems:'center',
            minHeight:'100vh',
            background:'#f0f2f5'
        }}>
            <Card style={{ width:380 }}>
                <Title level={3} style={{ textAlign: 'center', marginBottom:24}}>
                    FSMS Login
                </Title>
                <Form layout="vertical" onFinish={onFinish}>
                    <Form.Item
                      label="Email"
                      name="email"
                      rules={[{ required: true,message: 'Please enter your email}]}'
                    >
                     <Input placeholder="you@example.com" />
                </Form.Item>
                  label="password"
                  name="password"
                  rules={[{ required:true,message:'Please enter your password'}]}
                >
                 <Input.password placeholder="password" />
                </Form.Item>
                
                <Form.Item>
                 <Button type="primary" htmlType="submit" loading={loading} block>
                   Log In
                 </Button>
                </Form.Item>
            </Form>
        </Card>
    </div>
 );
}
 
