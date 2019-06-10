import { AgeCategory } from "./ageCategory/AgeCategory";
import { Command } from "./Command/Command";
import { Competition } from "./competition/Competition";
import { Gender } from "./gender/Gender";
import { GroupInStandardParent } from "./groupInStandardParent/GroupInStandardParent";
import { Participant } from "./participant/Participant";
import { ParticipantOnCompetition } from "./participantOnCompetition/ParticipantOnCompetition";
import { Position } from "./Position/Position";
import { RefereeOnTrialInCompetition } from "./RefereeOnTrialInCompetition/RefereeOnTrialInCompetition";
import { ResultGuide } from "./resultGuide/ResultGuide";
import { ResultParticipantOnTrial } from "./resultParticipantOnTrial/ResultParticipantOnTrial";
import { Role } from "./role/Role";
import { StandardParent } from "./standardParent/StandardParent";
import { Trial } from "./trial/Trial";
import { TrialOnCompetition } from "./trialOnCompetition/TrialOnCompetition";
import { TrialOnGroup } from "./trialOnGroup/TrialOnGroup";
import { Unit } from "./unit/Unit";
import { User } from "./user/User";
import { WorkerOfUser } from "./workerOfUser/WorkerOfUser";
import { WorkerOfUserInCompetition } from "./WorkerOfUserInCompetition/WorkerOfUserInCompetition";

export const models = [
	User,
	AgeCategory,
	Competition,
	Command,
	Gender,
	Unit,
	TrialOnCompetition,
	TrialOnGroup,
	Trial,
	StandardParent,
	Role,
	GroupInStandardParent,
	Participant,
	ParticipantOnCompetition,
	ResultGuide,
	ResultParticipantOnTrial,
	Position,
	WorkerOfUser,
	WorkerOfUserInCompetition,
	RefereeOnTrialInCompetition
];
