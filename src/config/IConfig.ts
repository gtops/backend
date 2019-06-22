export interface IConfig {
	projectName: string;
	server: {
		name: string;
		port: number;
		url: string;
	};
	database: {
		host: string;
		name: string;
		username: string;
		password: string;
		port: number;
		ssl?: boolean;
	};
	cors: {
		originUrls: string[];
		allowHeaders: string[];
		exposeHeaders: string[];
	};
	jwt: {
		secret: string;
		tokenTimeLive: string;
	};
	jwtInviteLink: {
		secret: string;
		tokenTimeLive: string;
	};
	addressFrontendServer: {
		url: string;
		port?: number;
	};
	other: {
		uidMask: string; // Only * (number) and - (symbol)
	};
	email: {
		addressFrom: string;
		SMTPData: {
			host: string;
			port: number;
			auth: {
				user: string;
				pass: string;
			}
		}
	};

}
