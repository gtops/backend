import { ERoles } from "../../middleware/interfaces/ERoles";

export interface IUserData {
	user_id: number;
	password: string;
	name_of_role: ERoles;
}
